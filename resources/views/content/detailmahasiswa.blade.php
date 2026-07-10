@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Mahasiswa')

@section('vendor-style')
    @vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
    @vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
    <script>
        window.weeklyOverviewData = {
            semester: @json(collect($gpa['data'] ?? [])->pluck('semester')->map(fn($semester) => 'Semester ' . $semester)->values()),
            ipk: @json(collect($gpa['data'] ?? [])->pluck('gpa')->map(fn($ipk) => (float) $ipk)->values())
        };
    </script>
    @vite('resources/assets/js/detailmahasiswa-gpa-chart.js')
@endsection

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('datamahasiswa') }}">Data Mahasiswa Wali</a>
            </li>
            <li class="breadcrumb-item active">Detail Mahasiswa</li>
        </ol>
    </nav>

    @include('content.biodata.biodata')

    @include('content.biodata.perwalian')
@endsection
