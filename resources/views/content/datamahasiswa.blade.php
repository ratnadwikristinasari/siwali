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
                        <form action="{{ route('datamahasiswa') }}" method="GET" id="form-filter">
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
                                <th class="text-truncate">Status Akademik</th>
                                <th class="text-truncate">Status Perwalian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mahasiswas as $index => $listmahasiswa)
                                <tr style="cursor: pointer;" onclick="window.location.href='#">
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
                                                Aktif
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
                                                Pending
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $mahasiswas->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
