@extends('layouts/contentNavbarLayout')

@section('title', 'Data Dosen')


@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#search').on('keyup', function() {
                let debounceTimer;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    $('#form-filter').submit();
                }, 1000);
            });

            $('#major_id').on('change', function() {
                $('#study_program_id').val('');
                $('#form-filter').submit();
            });
            $('#study_program_id').on('change', function() {
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
            <li class="breadcrumb-item active">Data Dosen</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="row g-2 align-items-center my-3 mx-1">
                    <div class="col-12 col-md">
                        <form action="{{ route('datadosen') }}" method="GET" id="form-filter">
                            <div class="row g-2 justify-content-md-end">
                                @if (auth()->user()->isSuperadmin())
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                        <div class="input-group input-group-sm">
                                            <select class="form-select" name="major_id" id="major_id">
                                                <option value="">Pilih Jurusan</option>
                                                @foreach ($majors as $major)
                                                    <option value="{{ $major['value'] }}"
                                                        {{ request('major_id') == $major['value'] ? 'selected' : '' }}>
                                                        {{ $major['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                @role('kajur')
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <input type="search" class="form-control" placeholder="Cari..." id="search"
                                            name="search" value="{{ request('search') }}">
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
                                <th class="text-truncate">NIP</th>
                                <th class="text-truncate">Prodi</th>
                                @if (auth()->user()->isSuperadmin())
                                    <th class="text-truncate text-center">Aksi</th>
                                @endif

                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dosens as $index => $listdosen)
                                <tr>
                                    <td>{{ $dosens->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-4">
                                                <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar"
                                                    class="rounded-circle">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{ $listdosen['name'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{ $listdosen['nip'] !== '' ? $listdosen['nip'] : '-' }}</td>
                                    <td class="text-truncate">
                                        {{ $listdosen['study_program']['name'] !== '' ? $listdosen['study_program']['name'] : '-' }}
                                    </td>
                                    @if (auth()->user()->isSuperadmin())
                                        <td class="text-center">
                                            <a href="{{ route('datamahasiswa', ['lecture_id' => $listdosen['id']]) }}"
                                                class="btn btn-sm btn-outline-primary" title="Data Mahasiswa Wali"
                                                aria-label="Data Mahasiswa Wali">
                                                <i class="ri-graduation-cap-fill"></i>
                                            </a>
                                            <a href="{{ route('dataperwaliandosen', ['lecture_id' => $listdosen['id']]) }}"
                                                class="btn btn-sm btn-outline-info" title="Perwalian Mahasiswa"
                                                aria-label="Perwalian Mahasiswa">
                                                <i class="ri-bank-card-2-line"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ ($needMajor ?? false) ? 'Pilih jurusan terlebih dahulu' : 'Data tidak ditemukan' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $dosens->links('pagination::bootstrap-page') }}
                </div>
            </div>
        </div>
    </div>
@endsection
