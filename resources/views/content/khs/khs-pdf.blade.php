<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Hasil Studi - {{ $data['student']['name'] }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        /* 1. HEADER (KOP SURAT) - Menggunakan Tabel agar Logo & Teks Bersebelahan Rapi */
        .header-table {
            width: 100%;
            border-bottom: 1px solid #999;
            /* Garis tipis di bawah kop */
            margin-bottom: 10px;
            padding-bottom: 10px;
        }

        .header-table td {
            border: none !important;
            /* Pastikan tidak ada border di kop */
            vertical-align: middle;
        }

        .logo-cell {
            width: 10%;
            /* Lebar kolom logo */
            text-align: center;
        }

        .logo-img {
            width: 100px;
            height: auto;
        }

        .text-cell {
            width: 90%;
            /* Sisa lebar untuk teks */
            text-align: center;
            /* Teks rata tengah */
        }

        .text-cell h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-cell p {
            margin: 1px 0;
            font-size: 12pt;
        }

        /* JUDUL */
        .title-section {
            text-align: center;
            margin-bottom: 0px;
            /* font-weight: bold; */
        }

        .title-section h3 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
        }

        /* INFO MAHASISWA */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            /* font-weight: bold; */
            font-size: 11pt;
            border: none;
        }

        .info-table td {
            padding: 0px;
            vertical-align: top;
            border: none !important;
            font-size: 11pt;
        }

        .label {
            width: 135px;
        }

        .sep {
            width: 0px;
        }

        /* 2. TABEL NILAI - Border Tegas */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            /* Wajib agar border menyatu */
            margin-bottom: 10px;
            font-size: 10pt;
        }

        .grades-table th,
        .grades-table td {
            border: 2.5px solid black !important;
            /* Border hitam tegas */
            padding: 0.5px;
            vertical-align: middle;
        }

        /* Warna Header Tabel (Cyan Muda - Mirip Gambar) */
        .grades-table th {
            background-color: #a8eeff;
            text-align: center;
            font-weight: bold;
        }

        /* Warna Footer Tabel (Biru Langit Tua - Mirip Gambar) */
        .bg-blue {
            background-color: #0099ff;
        }

        .catatan-row {
            height: 40px;
            /* Tinggi baris catatan */
            vertical-align: top !important;
            text-align: left !important;
            background-color: #0099ff;
            /* font-weight: bold; */
            padding-left: 5px;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        /* 3. FOOTER & TANDA TANGAN */
        .footer-container {
            width: 100%;
            margin-top: 10px;
            display: table;
            /* Layout tabel untuk footer */
        }

        .keterangan-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            font-size: 10pt;
        }

        .signature-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 50px;
            /* Geser blok ttd ke kanan */
        }

        /* Style Tanda Tangan Bertumpuk */
        .signature-block {
            margin-bottom: 20px;
            /* Jarak antara Kajur dan Dosen Wali */
            font-size: 11pt;
        }

        .signature-space {
            height: 100px;
            /* Ruang tanda tangan */
        }

        .bold-underline {
            font-weight: bold;
        }

        .grey {
            background-color: #e0e0e0;
        }

        .dark-grey {
            background-color: #bcbcbc;
        }

        /* PRINT MEDIA QUERY */
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grades-table th {
                background-color: #a8eeff !important;
            }

            .bg-blue,
            .catatan-row {
                background-color: #0099ff !important;
            }
        }

        /* by default ukuran A4 */
        @page {
            size: A4;
            margin: 1cm;
        }
    </style>
</head>

@php
    $gradeMapping = [
        'A' => 4.0,
        'AB' => 3.5,
        'B' => 3.0,
        'BC' => 2.5,
        'C' => 2.0,
        'D' => 1.0,
        'E' => 0.0,
    ];
@endphp

<body>

    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('assets/img/logo/polije.png') }}" alt="Logo" class="logo-img">
            </td>
            <td class="text-cell">
                <h2>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h2>
                <p>Jl. Mastrip PO.BOX 164 Telp 333532 - 333534 Fax 333531 Jember 68101</p>
                <p>Website : https://www.polije.ac.id E-Mail : politeknik@polije.ac.id</p>
            </td>
        </tr>
    </table>

    <div class="title-section">
        <h3>KARTU HASIL STUDI</h3>
        <div>SEMESTER {{ $data['semesters'][0]['semester'] % 2 === 1 ? 'GANJIL' : 'GENAP' }}
            {{ $academicYear['session']['session'] ?? '' }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">NAMA</td>
            <td class="sep">:</td>
            <td>{{ ucwords(strtolower($data['student']['name'])) ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td class="sep">:</td>
            <td>{{ $data['student']['nim'] }}</td>
        </tr>
        <tr>
            <td class="label">JURUSAN</td>
            <td class="sep">:</td>
            <td>{{ $data['student']['major'] ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">PROGRAM STUDI</td>
            <td class="sep">:</td>
            <td>{{ $data['student']['study_program'] }}</td>
        </tr>
        <tr>
            <td class="label">STATUS</td>
            <td class="sep">:</td>
            <td>
                {{ isset($data['student']['status']) && $data['student']['status'] === 'ACTIVE' ? 'Aktif' : 'Tanpa Keterangan' }}
            </td>
        </tr>
    </table>

    <table class="grades-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Kode</th>
                <th>Mata Kuliah</th>
                <th style="width: 8%;">HM</th>
                <th style="width: 8%;">AM</th>
                <th style="width: 8%;">K</th>
                <th style="width: 8%;">M</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_sks = 0;
                $total_mutu = 0;
                $sortedSubjects = collect($data['semesters'][0]['subjects'])->sortBy('subject_code');
            @endphp
            @foreach ($sortedSubjects as $mk)
                @php
                    $am = $gradeMapping[$mk['grade_letter']] ?? 0;
                    $k = $mk['credit'];
                    $m = $am * $k;
                    $total_sks += $k;
                    $total_mutu += $m;
                @endphp
                <tr>
                    <td class="center grey">{{ $loop->iteration }}</td>
                    <td class="center grey">{{ $mk['subject_code'] }}</td>
                    <td class="left grey">{{ strtoupper($mk['subject_name']) }}</td>
                    <td class="center dark-grey">{{ $mk['grade_letter'] }}</td>
                    <td class="center dark-grey">{{ $am }}</td>
                    <td class="center dark-grey">{{ $k }}</td>
                    <td class="center dark-grey">{{ $m }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="7" class="catatan-row">Catatan :</td>
            </tr>

            <tr class="bg-blue">
                <td colspan="5" class="center" style="font-weight: bold;">Jumlah</td>
                <td class="center">{{ $total_sks }}</td>
                <td class="center">{{ $total_mutu }}</td>
            </tr>

            <tr class="bg-blue">
                <td colspan="5" class="center" style="font-weight: bold;">Nilai Mutu Rata-Rata (M/K)</td>
                <td colspan="2" class="center">{{ $total_sks ? number_format($total_mutu / $total_sks, 2) : 0 }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer-container">

        <div class="keterangan-col">
            Keterangan :<br>
            HM = Huruf Mutu<br>
            AM = Angka Mutu<br>
            K<span style="color: white;">M</span> = Kredit<br>
            M &nbsp;&nbsp; = Mutu
        </div>

        <div class="signature-col">

            <div class="signature-block">
                <div>Jember, {{ now()->format('d-m-Y') }}</div>
                <div>KETUA JURUSAN TEKNOLOGI INFORMASI</div>
                <div class="signature-space">
                    @if (isset($eSignMajorHead['data']['qr_code_base64']))
                        <img src="{{ $eSignMajorHead['data']['qr_code_base64'] }}" style="width: auto; height: 100%;">
                    @endif
                </div>
                <div>{{ $majorHeadData['data']['head']['name'] ?? '' }}</div>
                <div>NIP. {{ $majorHeadData['data']['head']['nip'] ?? '' }}</div>
            </div>

            <div class="signature-block">
                <div>DOSEN WALI</div>
                <div class="signature-space">
                    @if (isset($eSign['data']['qr_code_base64']))
                        <img src="{{ $eSign['data']['qr_code_base64'] }}" style="width: auto; height: 100%;">
                    @endif
                </div>
                <div>{{ $lectureName ?? '' }}</div>
                <div>NIP. {{ $lecturerNip ?? '' }}</div>
            </div>

        </div>
    </div>

</body>

</html>
