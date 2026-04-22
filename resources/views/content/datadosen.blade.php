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

            $('#study_program_id').on('change', function() {
                $('#form-filter').submit();
            });
        });
    </script>
@endsection
@section('vendor-pagination')
    @vite('resources/views/vendor/pagination/bootstrap-page.blade.php')
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
                                    <td class="text-truncate">{{ $listdosen['nip'] }}</td>
                                @empty
                                    <td colspan="3" class="text-center">Data tidak ditemukan</td>
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
