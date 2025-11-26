<?php

use App\Http\Controllers\Controller;
 
 class SiwaliApiController extends Controller { 
   public function studyProgramOption(Request $request)
  {
    $majorId = $request->input('major_id');
    $user = Auth::user();
    $studyPrograms = ProdiHelper::getAsOptions($user->token, $majorId);

    if ($user->hasAnyRole('kaprodi')) {
      if (!empty($user->study_program_id)) {
        $studyPrograms['data'] = array_values(array_filter(
          $studyPrograms['data'],
          function ($studyProgram) use ($user) {
            return $studyProgram['value'] == $user->study_program_id;
          }
        ));
      }
    }

    return $studyPrograms;
  }
}