@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    @vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('vendor-pagination')
    @vite('resources/views/vendor/pagination/bootstrap-page.blade.php')
@endsection

@section('content')
    <h4>Data Mahasiswa</h4>
    <div class="col-12">
        <div class="card overflow-hidden">
            <div class="row g-2 align-items-center my-3 mx-1">
            <div class="col-12 col-md">
                <div class="row g-2 justify-content-md-end">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="input-group input-group-sm">
                            <input type="search" class="form-control" placeholder="Cari..." aria-label="Cari..."
                                id="search" name="search" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="text-truncate">No</th>
                            <th class="text-truncate">Nama</th>
                            <th class="text-truncate">NIM</th>
                            <th class="text-truncate">Program Studi</th>
                            <th class="text-truncate">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mahasiswas as $index => $listmahasiswa)
                            <tr>
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
                                    {{-- @dd($listmahasiswa) --}}
                                    @if ($listmahasiswa['status_perwalian'] === null)
                                        <span class="badge bg-label-secondary rounded-pill">
                                            Belum Perwalian
                                        </span>
                                    @elseif ($listmahasiswa['status_perwalian'] === 'pending')
                                        <span class="badge bg-label-warning rounded-pill">
                                            Pending
                                        </span>
                                    @else
                                        <span class="badge bg-label-success rounded-pill">
                                            Done
                                        </span>
                                    @endif
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $mahasiswas->links('pagination::bootstrap-page') }}
            </div>
        </div>
    </div>
@endsection
