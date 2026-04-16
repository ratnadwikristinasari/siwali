@extends('layouts.contentNavbarLayout')

@section('title', 'Mahasiswa wali')

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
                                <th class="text-truncate">Status Perwalian</th>
                                <th class="text-truncate">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mahasiswaall as $index => $allmahasiswa)
                                <tr>
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
                                            {{-- <span>{{ $allmahasiswa['study_program_name'] }}</span> --}}
                                        </div>
                                    </td>
                                    {{-- <td>
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
 <td>
                                        @if ($allmahasiswa['status'] === null)
                                            <span class="badge bg-label-secondary rounded-pill">
                                                Tanpa Keterangan
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'CUTI')
                                            <span class="badge bg-label-warning rounded-pill">
                                                Cuti
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'DO')
                                            <span class="badge bg-label-info rounded-pill">
                                                Drop Out
                                            </span>
                                        @elseif ($allmahasiswa['status'] === 'ACTIVE')
                                            <span class="badge bg-label-success rounded-pill">
                                                Active
                                            </span>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $mahasiswaall->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
