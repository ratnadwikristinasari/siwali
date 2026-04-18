<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Auth\UserLoginInfoDto;
use App\Dto\Auth\UserLoginResponseDto;
use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class OAuthController extends Controller
{
    public function redirect()
    {
        return redirect(config('app.super_app_url') . '/oauth/authorize?client_id=' . env('OAUTH_CLIENT_ID') . '&redirect_uri=' . route('auth.callback') . '&response_type=code');
    }

    public function callback(Request $request)
    {
        $response = Http::withoutVerifying()->asForm()->post(config('app.super_app_url') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('OAUTH_CLIENT_ID'),
            'client_secret' => env('OAUTH_CLIENT_SECRET'),
            'redirect_uri' => route('auth.callback'),
            'code' => $request->code,
        ]);

        if ($response->failed()) {
            return redirect()->route('login')->withErrors(['msg' => $response->json('message') ?? 'Login failed. Please try again.']);
        }

        $data = $response->json();

        $dto = new UserLoginResponseDto(
            token: $data['data']['token'],
            user: new UserLoginInfoDto(
                id: $data['data']['user']['id'],
                name: $data['data']['user']['name'],
                email: $data['data']['user']['email'],
                roles: $data['data']['user']['roles'] ?? null,
                permissions: $data['data']['user']['permissions'] ?? null,
            ),
        );

        $authData = AuthHelper::getauth(null, $dto->token);

        if (isset($authData['data']['employee_detail'])) {
            $majorId = $authData['data']['employee_detail']['m_major_id'] ?? null;
            $majorName = $authData['data']['employee_detail']['major_name'] ?? null;
            $studyProgramId = $authData['data']['employee_detail']['m_study_program_id'] ?? null;
            $studyProgramName = $authData['data']['employee_detail']['study_program_name'] ?? null;
            $studentEmployeeId = $authData['data']['employee_detail']['id'] ?? null;
        } else if (isset($authData['data']['student_detail'])) {
            $majorId = $authData['data']['student_detail']['m_major_id'] ?? null;
            $majorName = $authData['data']['student_detail']['major_name'] ?? null;
            $studyProgramId = $authData['data']['student_detail']['m_study_program_id'] ?? null;
            $studyProgramName = $authData['data']['student_detail']['study_program_name'] ?? null;
            $studentEmployeeId = $authData['data']['student_detail']['id'] ?? null;
        } else {
            $majorId = null;
            $majorName = null;
            $studyProgramId = null;
            $studyProgramName = null;
            $studentEmployeeId = null;
        }

        $user = User::updateOrCreate(
            ['external_id' => $dto->user->id],
            [
                'student_employee_id' => $studentEmployeeId,
                'name' => $dto->user->name,
                'email' => $dto->user->email,
                'token' => $dto->token,
                'major_id' => $majorId,
                'major' => $majorName,
                'study_program_id' => $studyProgramId,
                'study_program' => $studyProgramName,
                'roles' => $dto->user->roles ? $dto->user->roles : null,
                'permissions' => $dto->user->permissions ? $dto->user->permissions : null,
            ]
        );

        if (isset($authData['data']['student_detail'])) {
            $idParent = $authData['data']['student_detail']['parent']['parent_id'] ?? null;
            $userParentExternalId = $authData['data']['student_detail']['parent']['user_id'] ?? null;

            $userParent = User::updateOrCreate(
                ['external_id' => $userParentExternalId],
                [
                    'name' => 'Orang Tua dari ' . $dto->user->name,
                    'email' => 'ortu-' . explode('@', $dto->user->email)[0] . '@polije.ac.id',
                    'roles' => ['orang_tua'],
                ]
            );

            $userParent->studentParents()->updateOrCreate([
                'student_id' => $user->id,
                'parent_external_id' => $idParent,
                'parent_id' => $userParent->id,
            ]);
        }

        if (isset($authData['data']['parent_detail'])) {
            $children = $authData['data']['parent_detail']['students'] ?? [];
            $userParentExternalId = $authData['data']['parent_detail']['id'] ?? null;
            foreach ($children as $child) {
                $studentExternalId = $child['user_id'] ?? null;
                $student = User::updateOrCreate(
                    ['external_id' => $studentExternalId],
                    [
                        'student_employee_id' => $child['student_id'] ?? null,
                        'name' => $child['name'] ?? 'Anak dari ' . $dto->user->name,
                        'email' => $child['nim'] . '@student.polije.ac.id' ?? 'anak-' . explode('@', $dto->user->email)[0] . '@student.polije.ac.id',
                        'roles' => ['student'],
                    ]
                );

                $student->parentOf()->updateOrCreate([
                    'parent_id' => $user->id,
                    'parent_external_id' => $userParentExternalId,
                    'student_id' => $student->id,
                ]);
            }
        }


        Auth::login($user);
        return redirect()->route('content.dashboard.dashboard-main');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away(config('app.super_app_url') . '/oauth/logout?redirect=' . route('content.landingpage'));
    }
}
