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
        } else {
            $majorId = $authData['data']['student_detail']['m_major_id'] ?? null;
            $majorName = $authData['data']['student_detail']['major_name'] ?? null;
            $studyProgramId = $authData['data']['student_detail']['m_study_program_id'] ?? null;
            $studyProgramName = $authData['data']['student_detail']['study_program_name'] ?? null;
        }

        $user = User::updateOrCreate(
            ['external_id' => $dto->user->id],
            [
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
