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
                                                Aktif
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
                                                Pending
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data mahasiswa yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $mahasiswaall->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
