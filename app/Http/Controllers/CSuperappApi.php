<?php

namespace App\Http\Controllers;

use App\Helpers\SemesterApiHelper;
use App\Helpers\SessionApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CSuperappApi extends Controller
{
    public function semesterOption(Request $request)
    {
        $sessionID = $request->input('session_id');
        $semesters = SemesterApiHelper::getSemesterAsOption(Auth::user()->token, $sessionID);

        $semesters = array_map(function ($semester) {
            return [
            'label' => $semester['semester'],
            'value' => $semester['id']
            ];
        }, $semesters);

        return response()->json([
            'message' => 'Semesters fetched successfully',
            'data' => $semesters
        ]);
    }

      public function sessionOption()
        {
            $sessions = SessionApiHelper::getAsOptions(Auth::user()->token);
            return $sessions;
        }

}
