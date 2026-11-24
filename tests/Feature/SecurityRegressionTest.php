<?php

use App\Models\Advise;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

beforeEach(function () {
    Schema::create('users', function ($table) {
        $table->uuid('id')->primary();
        $table->string('external_id')->nullable();
        $table->string('student_employee_id')->nullable();
        $table->string('name')->nullable();
        $table->string('email')->nullable();
        $table->text('token')->nullable();
        $table->string('major_id')->nullable();
        $table->string('major')->nullable();
        $table->string('study_program_id')->nullable();
        $table->string('study_program')->nullable();
        $table->json('roles')->nullable();
        $table->json('permissions')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
    Schema::create('advise', function ($table) {
        $table->uuid('id')->primary();
        $table->string('student_user_id')->nullable();
        $table->string('lecture_user_id')->nullable();
        $table->string('student_id')->nullable();
        $table->string('lecture_id')->nullable();
        $table->string('type')->nullable();
        $table->string('status')->nullable();
        $table->string('semester')->nullable();
        $table->string('semester_id')->nullable();
        $table->string('keluhan')->nullable();
        $table->string('masukan')->nullable();
        $table->string('khs')->nullable();
        $table->timestamps();
    });
});

function mkUser(array $roles, array $extra = []): User
{
    return User::create(array_merge([
        'external_id' => (string) Str::uuid(),
        'name' => 'u',
        'email' => Str::random(6) . '@x.test',
        'token' => 'tok',
        'roles' => $roles,
    ], $extra));
}

function mkAdvise(User $lecturer, string $status = 'pending', string $type = 'non_gpa_advising'): Advise
{
    return Advise::create([
        'lecture_user_id' => $lecturer->id,
        'student_user_id' => mkUser(['student'])->id,
        'type' => $type,
        'status' => $status,
        'keluhan' => 'k',
    ]);
}

it('forbids another lecturer from completing a perwalian', function (string $route) {
    $owner = mkUser(['lecturer']);
    $other = mkUser(['lecturer']);
    $advise = mkAdvise($owner);

    $this->actingAs($other)
        ->put(route($route, $advise->id), ['masukan' => 'x'])
        ->assertForbidden();

    expect($advise->fresh()->status)->toBe('pending');
})->with(['perwalian.update', 'perwalian.nonkhs.update']);

it('rejects re-processing a perwalian that is no longer pending', function () {
    $owner = mkUser(['lecturer']);
    $advise = mkAdvise($owner, 'done');

    $this->actingAs($owner)
        ->put(route('perwalian.update', $advise->id), ['masukan' => 'x'])
        ->assertStatus(409);
});

it('forbids a student from calling lecturer-only routes', function () {
    $student = mkUser(['student']);
    $advise = mkAdvise(mkUser(['lecturer']));

    $this->actingAs($student)->put(route('perwalian.nonkhs.update', $advise->id), ['masukan' => 'x'])->assertForbidden();
    $this->actingAs($student)->get(route('perwalian.edit', $advise->id))->assertForbidden();
});

it('forbids lecturers from the student non-KHS submit route', function () {
    $this->actingAs(mkUser(['lecturer']))
        ->post(route('perwalian.nonkhs'), ['keluhan' => 'x'])
        ->assertForbidden();
});

it('forbids a lecturer from viewing another lecturer perwalian detail', function () {
    $advise = mkAdvise(mkUser(['lecturer']));

    $this->actingAs(mkUser(['lecturer']))
        ->get(route('perwalian.detail', $advise->id))
        ->assertForbidden();
});

it('only queues signing for signed gpa_advising', function () {
    Queue::fake();
    $kajur = mkUser(['kajur']);
    $lecturer = mkUser(['lecturer']);
    $signed = mkAdvise($lecturer, 'signed', 'gpa_advising');
    $done = mkAdvise($lecturer, 'done', 'gpa_advising');

    $this->actingAs($kajur)->post(route('page.need_sign.sign', $done->id))->assertStatus(409);
    Queue::assertNothingPushed();

    $this->actingAs($kajur)->post(route('page.need_sign.sign_bulk'), ['ids' => [$signed->id, $done->id]]);
    Queue::assertPushed(\App\Jobs\ProcessSignDocument::class, 1);
    expect($done->fresh()->status)->toBe('done');
    expect($signed->fresh()->status)->toBe('processing');
});

it('forbids previewing another student KHS', function () {
    $student = mkUser(['student'], ['student_employee_id' => 'own']);
    Http::fake();

    $this->actingAs($student)
        ->get(route('biodata.preview-gpa', ['studentId' => 'someone-else', 'semesterId' => 's1']))
        ->assertForbidden();
});

it('rejects OAuth callback without a matching state', function (?string $sessionState, array $query) {
    Http::fake();

    $this->withSession($sessionState ? ['oauth_state' => $sessionState] : [])
        ->get(route('auth.callback', $query))
        ->assertRedirect(route('login'));

    Http::assertNothingSent();
})->with([
    'no state at all' => [null, ['code' => 'c']],
    'missing state param' => ['abc', ['code' => 'c']],
    'wrong state' => ['abc', ['code' => 'c', 'state' => 'zzz']],
]);

it('sends a state param on OAuth redirect and stores it in session', function () {
    $res = $this->get(route('auth.login'));

    $state = session('oauth_state');
    expect($state)->toBeString()->not->toBeEmpty();
    expect($res->headers->get('Location'))->toContain('state=' . $state);
});

it('rejects non-UUID student ids on supervisor admin routes', function () {
    Http::fake();
    $admin = mkUser(['superadmin']);

    $this->actingAs($admin)->get(route('admin.supervisors.show', '../x'))->assertNotFound();
    Http::assertNothingSent();
});
