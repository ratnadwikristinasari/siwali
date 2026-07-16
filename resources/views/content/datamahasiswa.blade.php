@extends('layouts.contentNavbarLayout')

@section('title', 'Mahasiswa wali')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let initialSessionId = '{{ request('session_id') }}';
            let initialSemesterId = '{{ request('semester_id') }}';

            if (initialSessionId) {
                $.ajax({
                    url: '{{ route('api.semester.option') }}',
                    method: 'GET',
                    data: {
                        session_id: initialSessionId
                    },
                    success: function(response) {
                        let semesterSelect = $('#semester_id');
                        semesterSelect.empty();
                        semesterSelect.append('<option value="">Pilih Semester</option>');
                        response.data.forEach(function(semester) {
                            let selected = (semester.value == initialSemesterId) ? 'selected' :
                                '';
                            semesterSelect.append('<option value="' + semester.value + '" ' +
                                selected + '>' +
                                semester.label + '</option>');
                        });
                        semesterSelect.prop('disabled', false);
                    }
                });
            }

            $('#search').on('keyup', function() {
                let debounceTimer;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    $('#form-filter').submit();
                }, 1000);
            });

            $('#session_id').on('change', function() {
                let sessionId = $(this).val();

                if (sessionId) {
                    $.ajax({
                        url: '{{ route('api.semester.option') }}',
                        method: 'GET',
                        data: {
                            session_id: sessionId
                        },
                        success: function(response) {
                            let semesterSelect = $('#semester_id');
                            semesterSelect.empty();
                            semesterSelect.append('<option value="">Pilih Semester</option>');
                            response.data.forEach(function(semester) {
                                semesterSelect.append('<option value="' + semester
                                    .value + '">' + semester
                                    .label + '</option>');
                            });
                            semesterSelect.prop('disabled', false);
                            $('#form-filter').submit();
                        },
                        error: function() {
                            alert('Gagal mengambil data semester');
                        }
                    });
                } else {
                    $('#semester_id').empty().append('<option value="">Pilih Semester</option>').prop(
                        'disabled',
                        true);
                    $('#form-filter').submit();
                }
            });

            $('#semester_id').on('change', function() {
                $('#form-filter').submit();
            });

            $('#status_akademik').on('change', function() {
                $('#form-filter').submit();
            });

            $('#status_perwalian').on('change', function() {
                $('#form-filter').submit();
            });

            const whatsappModalElement = document.getElementById('whatsappTemplateModal');
            const whatsappForm = document.getElementById('whatsapp-template-form');
            const whatsappTargetName = document.getElementById('whatsapp-target-name');
            const whatsappTemplate = document.getElementById('whatsapp-template');
            const openWhatsappButton = document.getElementById('open-whatsapp-btn');

            if (whatsappModalElement && whatsappForm) {
                const whatsappModal = new bootstrap.Modal(whatsappModalElement);

                document.querySelectorAll('.btn-open-whatsapp-template').forEach(function(button) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const phoneNumber = this.dataset.phone || '';
                        const studentName = this.dataset.name || 'Mahasiswa';
                        const studentNim = this.dataset.nim || '-';

                        whatsappForm.dataset.phone = phoneNumber;
                        whatsappForm.dataset.name = studentName;
                        whatsappForm.dataset.nim = studentNim;
                        whatsappTargetName.textContent = studentName + ' (' + studentNim + ')';
                        whatsappTemplate.value = '';

                        openWhatsappButton.disabled = !phoneNumber;

                        whatsappModal.show();
                    });
                });

                whatsappForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const phoneNumberRaw = whatsappForm.dataset.phone || '';
                    const studentName = whatsappForm.dataset.name || 'Mahasiswa';
                    const template = whatsappTemplate.value;

                    if (!phoneNumberRaw || !template) {
                        return;
                    }

                    const normalizeWhatsappPhone = function(rawPhone) {
                        const cleaned = (rawPhone || '').replace(/[^\d+]/g, '');

                        if (!cleaned) {
                            return '';
                        }

                        // Keep international format that already has country code.
                        if (cleaned.startsWith('+')) {
                            return cleaned.slice(1).replace(/\D/g, '');
                        }

                        const digitsOnly = cleaned.replace(/\D/g, '');

                        if (!digitsOnly) {
                            return '';
                        }

                        // Indonesian local format: 08xx -> 628xx.
                        if (digitsOnly.startsWith('0')) {
                            return '62' + digitsOnly.slice(1);
                        }

                        // Already has country code (62 or others): keep as-is.
                        return digitsOnly;
                    };

                    const phoneNumber = normalizeWhatsappPhone(phoneNumberRaw);

                    if (!phoneNumber) {
                        return;
                    }

                    let message = '';

                    if (template === 'umum') {
                        message = 'Halo ' + studentName +
                            ', Berdasarkan Kalender Akademik Jurusan Teknologi Informasi Politeknik Negeri Jember, harap segera melakukan perwalian semester. Apabila belum mengisi Form Evaluasi Dosen, mohon segera melengkapi Form Evaluasi Dosen dan melakukan pengajuan form perwalian KHS. Terima kasih';
                    } else if (template === 'hari-ini') {
                        message = 'Halo ' + studentName +
                            ', Berdasarkan Kalender Akademik Jurusan Teknologi Informasi Politeknik Negeri Jember, harap melakukan perwalian semester hari ini, Terimakasih';
                    } else if (template === 'final') {
                        message = 'Halo ' + studentName +
                            ', Berdasarkan Kalender Akademik Jurusan Teknologi Informasi Politeknik Negeri Jember, Mohon segera melakukan perwalian secepatnya.';
                    }

                    const whatsappUrl = 'https://wa.me/' + phoneNumber + '?text=' + encodeURIComponent(
                        message);
                    window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
                    whatsappModal.hide();
                });
            }
        });
    </script>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Mahasiswa Wali</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="row g-2 align-items-center my-3 mx-1">
                    <div class="col-12 col-md">
                        <form action="{{ route('datamahasiswa') }}" method="GET" id="form-filter">
                            <div class="row g-2 justify-content-md-end">
                                <div class="col-12 col-sm-6 col-md-4 col-lg">
                                    <div class="input-group input-group-sm">
                                        <select class="form-select" name="session_id" id="session_id">
                                            <option value="">Pilih Tahun Ajaran</option>
                                            @foreach ($sessions as $session)
                                                <option value="{{ $session['value'] }}"
                                                    {{ request('session_id') == $session['value'] ? 'selected' : '' }}>
                                                    {{ $session['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg">
                                    <div class="input-group input-group-sm">
                                        <select class="form-select" name="semester_id" id="semester_id" disabled>
                                            <option value="">Pilih Semester</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg">
                                    <div class="input-group input-group-sm">
                                        <select class="form-select" name="status_akademik" id="status_akademik">
                                            <option value="">Pilih Status Akademik</option>
                                            <option value="AKTIF"
                                                {{ request('status_akademik') == 'AKTIF' ? 'selected' : '' }}>Naik
                                            </option>
                                            <option value="CUTI"
                                                {{ request('status_akademik') == 'CUTI' ? 'selected' : '' }}>Cuti</option>
                                            <option value="DO"
                                                {{ request('status_akademik') == 'DO' ? 'selected' : '' }}>Drop Out
                                            </option>
                                            <option value="MENGUNDURKAN_DIRI"
                                                {{ request('status_akademik') == 'MENGUNDURKAN_DIRI' ? 'selected' : '' }}>
                                                Mengundurkan Diri</option>
                                            <option value="LULUS"
                                                {{ request('status_akademik') == 'LULUS' ? 'selected' : '' }}>Lulus
                                            </option>
                                            <option value="MENINGGAL"
                                                {{ request('status_akademik') == 'MENINGGAL' ? 'selected' : '' }}>Meninggal
                                            </option>
                                            <option value="STUDENT EXCHANGE"
                                                {{ request('status_akademik') == 'STUDENT EXCHANGE' ? 'selected' : '' }}>
                                                Student Exchange</option>
                                            <option value="TANPA_KETERANGAN"
                                                {{ request('status_akademik') == 'TANPA_KETERANGAN' ? 'selected' : '' }}>
                                                Tanpa Keterangan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg">
                                    <div class="input-group input-group-sm">
                                        <select class="form-select" name="status_perwalian" id="status_perwalian">
                                            <option value="">Pilih Status Perwalian</option>
                                            <option value="belum"
                                                {{ request('status_perwalian') == 'belum' ? 'selected' : '' }}>Belum
                                                Perwalian
                                            </option>
                                            <option value="pending"
                                                {{ request('status_perwalian') == 'pending' ? 'selected' : '' }}>Menunggu
                                                Tanda
                                                Tangan Dosen Wali</option>
                                            <option value="signed"
                                                {{ request('status_perwalian') == 'signed' ? 'selected' : '' }}>Menunggu
                                                Tanda
                                                Tangan Kajur</option>
                                            <option value="done"
                                                {{ request('status_perwalian') == 'done' ? 'selected' : '' }}>Selesai
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <input type="search" class="form-control" placeholder="Cari berdasarkan nama"
                                            id="search" name="search" value="{{ request('search') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-truncate">No</th>
                                <th class="text-truncate">Nama</th>
                                <th class="text-truncate">NIM</th>
                                <th class="text-truncate">Program Studi</th>
                                <th class="text-truncate">Status Akademik</th>
                                <th class="text-truncate">Status Perwalian</th>
                                <th class="text-truncate text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mahasiswas as $index => $listmahasiswa)
                                <tr style="cursor: pointer;"
                                    onclick="window.location.href='{{ route('detailmahasiswa', $listmahasiswa['student_id']) }}'">
                                    <td>{{ $mahasiswas->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{ $listmahasiswa['name'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{ $listmahasiswa['nim'] }}</td>
                                    <td class="text-truncate">
                                        <div class="d-flex align-items-center">
                                            <span>{{ $listmahasiswa['study_program_name'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($listmahasiswa['status'] === null || $listmahasiswa['status'] === 'TANPA KETERANGAN')
                                            <span class="badge bg-label-secondary rounded-pill">
                                                Tanpa Keterangan
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'AKTIF')
                                            <span class="badge bg-label-success rounded-pill">
                                                Naik
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'CUTI')
                                            <span class="badge bg-label-warning rounded-pill">
                                                Cuti
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'DO')
                                            <span class="badge bg-label-info rounded-pill">
                                                Drop Out
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'MENGUNDURKAN_DIRI')
                                            <span class="badge bg-label-danger rounded-pill">
                                                Mengundurkan Diri
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'LULUS')
                                            <span class="badge bg-label-primary rounded-pill">
                                                Lulus
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'MENINGGAL')
                                            <span class="badge bg-label-dark rounded-pill">
                                                Meninggal
                                            </span>
                                        @elseif ($listmahasiswa['status'] === 'STUDENT EXCHANGE')
                                            <span class="badge bg-label-info rounded-pill">
                                                Student Exchange
                                            </span>
                                        @else
                                            <span class="badge bg-label-success rounded-pill">
                                                {{ $listmahasiswa['status'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($listmahasiswa['status_perwalian'] === null)
                                            <span class="badge bg-label-secondary rounded-pill">
                                                Belum Perwalian
                                            </span>
                                        @elseif ($listmahasiswa['status_perwalian'] === 'pending')
                                            <span class="badge bg-label-warning rounded-pill">
                                                Menunggu Tanda Tangan Dosen Wali
                                            </span>
                                        @elseif ($listmahasiswa['status_perwalian'] === 'signed')
                                            <span class="badge bg-label-info rounded-pill">
                                                Menunggu Tanda Tangan Kajur
                                            </span>
                                        @elseif ($listmahasiswa['status_perwalian'] === 'done')
                                            <span class="badge bg-label-success rounded-pill">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1"
                                            onclick="event.stopPropagation();">
                                            @if (!empty($listmahasiswa['active_semester_id']))
                                                <form
                                                    action="{{ route('datamahasiswa.reminder', ['studentId' => $listmahasiswa['student_id'], 'semesterId' => $listmahasiswa['active_semester_id']]) }}"
                                                    method="POST" class="m-0" onclick="event.stopPropagation();">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $listmahasiswa['can_send_reminder'] ? 'btn-outline-primary' : 'btn-outline-secondary' }}"
                                                        {{ $listmahasiswa['can_send_reminder'] ? '' : 'disabled' }}
                                                        title="{{ $listmahasiswa['can_send_reminder'] ? 'Kirim pengingat email' : 'Pengingat email sudah dikirim hari ini' }}"
                                                        aria-label="Kirim pengingat email"
                                                        onclick="event.stopPropagation();">
                                                        <i class="ri-mail-send-line"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                                    title="Semester aktif tidak tersedia"
                                                    aria-label="Semester aktif tidak tersedia">
                                                    <i class="ri-mail-send-line"></i>
                                                </button>
                                            @endif

                                            @php
                                                $rawPhone =
                                                    $listmahasiswa['phone_number'] ??
                                                    ($listmahasiswa['phone'] ??
                                                        ($listmahasiswa['mobile_phone'] ?? null));
                                            @endphp
                                            <button type="button"
                                                class="btn btn-sm {{ !empty($rawPhone) ? 'btn-outline-success' : 'btn-outline-secondary' }} btn-open-whatsapp-template"
                                                data-phone="{{ $rawPhone }}"
                                                data-name="{{ $listmahasiswa['name'] }}"
                                                data-nim="{{ $listmahasiswa['nim'] }}"
                                                {{ !empty($rawPhone) ? '' : 'disabled' }}
                                                title="{{ !empty($rawPhone) ? 'Kirim WhatsApp' : 'Nomor WhatsApp tidak tersedia' }}"
                                                aria-label="Kirim WhatsApp" onclick="event.stopPropagation();">
                                                <i class="ri-whatsapp-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $mahasiswas->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="whatsappTemplateModal" tabindex="-1" aria-labelledby="whatsappTemplateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="whatsapp-template-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="whatsappTemplateModalLabel">Pilih Template Pesan WhatsApp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Tujuan: <span id="whatsapp-target-name" class="fw-semibold"></span></p>
                        <label for="whatsapp-template" class="form-label">Template Pesan</label>
                        <select class="form-select" id="whatsapp-template" required>
                            <option value="" selected disabled>Pilih template</option>
                            <option value="umum">Pengingat perwalian umum</option>
                            <option value="hari-ini">Mohon perwalian hari ini</option>
                            <option value="final">Pengingat terakhir perwalian</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="open-whatsapp-btn">Buka WhatsApp</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
