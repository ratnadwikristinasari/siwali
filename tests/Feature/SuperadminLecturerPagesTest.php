<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

// migrations are MySQL-only: build the minimal table the sidebar composer queries
beforeEach(function () {
    Schema::create('users', function ($table) {
        $table->id();
        $table->string('name')->nullable();
        $table->timestamps();
    });
    Schema::create('advise', function ($table) {
        $table->id();
        $table->string('lecture_user_id')->nullable();
        $table->string('student_user_id')->nullable();
        $table->string('lecture_id')->nullable();
        $table->string('semester_id')->nullable();
        $table->string('type')->nullable();
        $table->string('status')->nullable();
        $table->string('keluhan')->nullable();
        $table->string('masukan')->nullable();
        $table->string('ipk')->nullable();
        $table->timestamps();
    });
});

function userWithRoles(array $roles): User
{
    return User::factory()->make([
        'roles' => $roles,
        'token' => 'token',
        'major_id' => null,
    ]);
}

it('lets superadmin roles detect themselves', function (string $role) {
    expect(userWithRoles([$role])->isSuperadmin())->toBeTrue();
    expect(userWithRoles(['lecturer'])->isSuperadmin())->toBeFalse();
})->with(['superadmin', 'superadmin_siwali']);

it('redirects superadmin to lecturer list when lecture_id is missing', function (string $route) {
    $this->actingAs(userWithRoles(['superadmin_siwali']))
        ->get(route($route))
        ->assertRedirect(route('datadosen'));
})->with(['datamahasiswa', 'dataperwaliandosen']);

it('forwards lecture_id to the students API for superadmin', function () {
    Http::fake(['*' => Http::response(['data' => [], 'meta' => ['total' => 0, 'per_page' => 10, 'page' => 1]])]);

    $this->actingAs(userWithRoles(['superadmin']))
        ->get(route('datamahasiswa', ['lecture_id' => 'abc']))
        ->assertOk();

    Http::assertSent(fn ($r) => str_contains($r->url(), 'by-lecture') && str_contains($r->url(), 'lecture_id=abc'));
});

it('blocks non-lecturer non-superadmin roles', function (string $route) {
    $this->actingAs(userWithRoles(['student']))
        ->get(route($route))
        ->assertForbidden();
})->with(['datamahasiswa', 'dataperwaliandosen']);

it('hides lecturer-only menus from superadmin sidebar', function () {
    Http::fake(['*' => Http::response(['data' => []])]);

    $this->actingAs(userWithRoles(['superadmin']))
        ->get(route('datadosen', ['major_id' => '']))
        ->assertOk()
        ->assertDontSee('Data Mahasiswa Wali')
        ->assertDontSee('Perwalian Mahasiswa');
});

it('filters advises by the selected lecturer for superadmin', function () {
    Http::fake(['*' => Http::response(['data' => []])]);
    DB::table('users')->insert(['id' => 1, 'name' => 'Mhs']);
    DB::table('advise')->insert([
        ['student_user_id' => '1', 'lecture_id' => 'abc', 'status' => 'pending', 'type' => 'gpa_advising', 'keluhan' => 'KELUHAN-ABC'],
        ['student_user_id' => '1', 'lecture_id' => 'xyz', 'status' => 'pending', 'type' => 'gpa_advising', 'keluhan' => 'KELUHAN-XYZ'],
    ]);

    $this->actingAs(userWithRoles(['superadmin']))
        ->get(route('dataperwaliandosen', ['lecture_id' => 'abc']))
        ->assertOk()
        ->assertViewHas('perwaliandosen', fn ($p) => $p->count() === 1 && $p->first()->lecture_id === 'abc');
});
