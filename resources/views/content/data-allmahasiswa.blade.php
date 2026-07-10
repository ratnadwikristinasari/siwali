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
                                selected + '>' + semester.label + '</option>');
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

            $('#study_program_id').on('change', function() {
                $('#form-filter').submit();
            });

            $('#class').on('change', function() {
                $('#form-filter').submit();
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
                                    .value + '">' + semester.label + '</option>');
                            });
                            semesterSelect.prop('disabled', false);
                        },
                        error: function() {
                            alert('Gagal mengambil data semester');
                        }
                    });
                } else {
                    $('#semester_id').empty().append('<option value="">Pilih Semester</option>').prop(
                        'disabled', true);
                }
            });

            $('#semester_id').on('change', function() {
                $('#form-filter').submit();
            });

            $('#status_akademik').on('change', function() {
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

                        if (cleaned.startsWith('+')) {
                            return cleaned.slice(1).replace(/\D/g, '');
                        }

                        const digitsOnly = cleaned.replace(/\D/g, '');

                        if (!digitsOnly) {
                            return '';
                        }

                        if (digitsOnly.startsWith('0')) {
                            return '62' + digitsOnly.slice(1);
                        }

                        return digitsOnly;
                    };

                    const phoneNumber = normalizeWhatsappPhone(phoneNumberRaw);

                    if (!phoneNumber) {
                        return;
                    }

                    let message = '';

                    if (template === 'umum') {
                        message = 'Halo ' + studentName +
                            ', ini pengingat untuk segera melakukan perwalian semester aktif. Mohon segera lengkapi form perwalian ya.';
                    } else if (template === 'hari-ini') {
                        message = 'Halo ' + studentName +
                            ', mohon melakukan perwalian hari ini agar proses akademik dapat segera diproses. Terima kasih.';
                    } else if (template === 'final') {
                        message = 'Halo ' + studentName +
                            ', ini pengingat terakhir untuk perwalian semester aktif. Mohon segera melakukan perwalian secepatnya.';
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
                        <form action="{{ route('alldatamahasiswa') }}" method="GET" id="form-filter">
                            <div class="row g-2 justify-content-md-end">
                                @role('kajur')
                                    <div class="col-12 col-sm-6 col-md-4 col-lg">
                                        <div class="input-group input-group-sm">
                                            <select class="form-select" name="study_program_id" id="study_program_id">
                                                <option value="">Pilih Program Studi</option>
                                                @foreach ($studyPrograms as $studyProgram)
                                                    <option value="{{ $studyProgram['id'] }}"
                                                        {{ request('study_program_id') == $studyProgram['id'] ? 'selected' : '' }}>
                                                        {{ $studyProgram['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endrole
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
                                        <select class="form-select" name="class" id="class">
                                            <option value="">Pilih Kelas</option>
                                            @foreach (App\Helpers\ClassHelper::getClasses() as $class)
                                                <option value="{{ $class }}"
                                                    {{ request('class') == $class ? 'selected' : '' }}>
                                                    {{ $class }}
                                                </option>
                                            @endforeach
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
                            @forelse ($mahasiswaall as $index => $allmahasiswa)
                                <tr style="cursor: pointer;"
                                    onclick="window.location.href='{{ route('detailmahasiswa', $allmahasiswa['id']) }}'">
                                    <td>{{ $mahasiswaall->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{ $allmahasiswa['name'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{ $allmahasiswa['nim'] }}</td>
                                    <td class="text-truncate">
                                        <div class="d-flex align-items-center">
                                            <span>{{ $allmahasiswa['study_program']['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($allmahasiswa['status'] === null || $allmahasiswa['status'] === 'TANPA KETERANGAN')
                                            <span class="badge bg-label-secondary rounded-pill">
                                                Tanpa Keterangan
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'AKTIF')
                                            <span class="badge bg-label-success rounded-pill">
                                                Naik
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'CUTI')
                                            <span class="badge bg-label-warning rounded-pill">
                                                Cuti
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'DO')
                                            <span class="badge bg-label-info rounded-pill">
                                                Drop Out
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'MENGUNDURKAN_DIRI')
                                            <span class="badge bg-label-danger rounded-pill">
                                                Mengundurkan Diri
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'LULUS')
                                            <span class="badge bg-label-primary rounded-pill">
                                                Lulus
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'MENINGGAL')
                                            <span class="badge bg-label-dark rounded-pill">
                                                Meninggal
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'STUDENT EXCHANGE')
                                            <span class="badge bg-label-info rounded-pill">
                                                Student Exchange
                                            </span>
                                        @else
                                            <span class="badge bg-label-success rounded-pill">
                                                {{ $allmahasiswa['status'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($allmahasiswa['status_perwalian'] === null)
                                            <span class="badge bg-label-secondary rounded-pill">
                                                Belum Perwalian
                                            </span>
                                        @elseif ($allmahasiswa['status_perwalian'] === 'pending')
                                            <span class="badge bg-label-warning rounded-pill">
                                                Menunggu Tanda Tangan Dosen Wali
                                            </span>
                                        @elseif ($allmahasiswa['status_perwalian'] === 'signed')
                                            <span class="badge bg-label-info rounded-pill">
                                                Menunggu Tanda Tangan Kajur
                                            </span>
                                        @elseif ($allmahasiswa['status_perwalian'] === 'done')
                                            <span class="badge bg-label-success rounded-pill">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1"
                                            onclick="event.stopPropagation();">
                                            @if (!empty($allmahasiswa['active_semester_id']))
                                                <form
                                                    action="{{ route('datamahasiswa.reminder', ['studentId' => $allmahasiswa['id'], 'semesterId' => $allmahasiswa['active_semester_id']]) }}"
                                                    method="POST" class="m-0" onclick="event.stopPropagation();">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm {{ $allmahasiswa['can_send_reminder'] ? 'btn-outline-primary' : 'btn-outline-secondary' }}"
                                                        {{ $allmahasiswa['can_send_reminder'] ? '' : 'disabled' }}
                                                        title="{{ $allmahasiswa['can_send_reminder'] ? 'Kirim pengingat email' : 'Pengingat email sudah dikirim hari ini' }}"
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
                                                    $allmahasiswa['phone_number'] ??
                                                    ($allmahasiswa['phone'] ?? ($allmahasiswa['mobile_phone'] ?? null));
                                            @endphp
                                            <button type="button"
                                                class="btn btn-sm {{ !empty($rawPhone) ? 'btn-outline-success' : 'btn-outline-secondary' }} btn-open-whatsapp-template"
                                                data-phone="{{ $rawPhone }}" data-name="{{ $allmahasiswa['name'] }}"
                                                data-nim="{{ $allmahasiswa['nim'] }}"
                                                {{ !empty($rawPhone) ? '' : 'disabled' }}
                                                title="{{ !empty($rawPhone) ? 'Kirim WhatsApp' : 'Nomor WhatsApp tidak tersedia' }}"
                                                aria-label="Kirim WhatsApp" onclick="event.stopPropagation();">
                                                <i class="ri-whatsapp-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data mahasiswa yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $mahasiswaall->links('vendor.pagination.bootstrap-5') }}
                </div>
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
