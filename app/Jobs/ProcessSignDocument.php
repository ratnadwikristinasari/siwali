<?php

namespace App\Jobs;

use App\Helpers\DosenHelper;
use App\Helpers\ESignApiHelper;
use App\Helpers\FileHelper;
use App\Helpers\MahasiswaHelper;
use App\Helpers\MajorHelper;
use App\Helpers\SemesterApiHelper;
use App\Models\Advise;
use App\Services\NotificationPublisher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProcessSignDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $adviseId, public string $token)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $advise = Advise::with('lecture', 'student')->findOrFail($this->adviseId);
        $token = $this->token;

        $supervisorData = DosenHelper::getLectureByUserId($token, $advise->lecture->external_id ?? '');
        $studentScore = MahasiswaHelper::getStudentScore($token, $advise->student_id ?? '', $advise->semester_id);
        $majorHeadData = MajorHelper::getById($token, $advise->lecture->major_id ?? '');
        $academicYear = SemesterApiHelper::getById($token, $advise->semester_id ?? '');

        $data = $studentScore['data'] ?? [];
        $filename = 'khs_' . $advise->student->external_id . '_' . $advise->semester . '_' . time() . '.pdf';

        FileHelper::deleteFile('khs_files', $advise->khs);

        $eSign = ESignApiHelper::signDocument($token, $filename, $supervisorData['user']['name'] ?? 'N/A');
        $eSignMajorHead = ESignApiHelper::signDocument($token, $filename, $majorHeadData['data']['head']['name'] ?? 'N/A');

        $lectureName = $supervisorData['user']['name'] ?? 'N/A';
        $lecturerNip = $supervisorData['nip'] ?? 'N/A';

        $pdf = Pdf::loadView('content.khs.khs-pdf', compact(
            'data', 'academicYear', 'majorHeadData', 'lectureName', 'lecturerNip', 'eSignMajorHead', 'eSign'
        ))->setPaper('a4', 'portrait');
        $pdfContent = $pdf->output();

        if (isset($eSign['data']['id'])) {
            ESignApiHelper::updateDocumentHash($token, $eSign['data']['id'], hash('sha256', $pdfContent));
        }

        $signedPdfContent = ESignApiHelper::signPDF($token, $pdfContent, $lectureName, 'Dosen Wali');

        if (isset($eSignMajorHead['data']['id'])) {
            ESignApiHelper::updateDocumentHash($token, $eSignMajorHead['data']['id'], hash('sha256', $signedPdfContent));
        }

        $fullySignedPdfContent = ESignApiHelper::signPDF($token, $signedPdfContent, $majorHeadData['data']['head']['name'] ?? 'N/A', 'Ketua Jurusan');

        Storage::disk('s3')->put('khs_files/' . $filename, $fullySignedPdfContent);

        $advise->update([
            'status' => 'done',
            'khs' => $filename
        ]);

        $publisher = app(NotificationPublisher::class);
        $publisher->send([
            'app_env' => config('app.env') == 'production' ? 'production' : 'dev',
            'event' => 'advise-signed-by-kajur-for-student',
            'recipient' => ['email' => strtolower($advise->student->email)],
            'channels' => ['email'],
            'subject' => 'Perwalian Selesai - Tanda Tangan Kajur Telah Diterapkan',
            'data' => [
                'name' => $advise->student->name ?? '',
                'advisor_note' => $advise->masukan ?? '',
                'form_url' => route('dataperwalian'),
            ],
        ]);

        // todo: send notif to parent if email exists
    }
}
