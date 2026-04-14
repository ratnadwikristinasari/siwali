@extends('layouts.contentNavbarLayout')

@section('title', 'Biodata Mahasiswa')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Biodata Mahasiswa</li>
        </ol>
    </nav>
{{-- Biodata Mahasiswa --}}
<div class="col-xl-12 col-md-6">
    <div class="card">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap">
            <div class="avatar avatar-lg">
                <div class="avatar-initial bg-primary rounded-circle fs-4">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                <small class="text-muted">NIM: {{ $userData['nim']?? '_' }}</small>
            </div>
            <span class="badge bg-label-success">Aktif</span>
        </div>

        <div class="card-body">
            {{-- Informasi Akademik --}}
            <p class="text-uppercase fw-semibold text small mb-2">Informasi Akademik</p>
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Program Studi</small>
                        <span class="fw-medium">{{ $userData->study_program['name'] ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Fakultas</small>
                        <span class="fw-medium">{{ $mahasiswa->fakultas ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Dosen Wali</small>
                        <span class="fw-medium">{{ $userData->dosen_wali ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Tahun Masuk</small>
                        <span class="fw-medium">{{ $userData->generation ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Informasi Pribadi --}}
            <p class="text-uppercase fw-semibold text small mb-2">Informasi Pribadi</p>
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Email</small>
                        <span class="fw-medium text-primary">{{ Auth::user()->email }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">No. Telepon</small>
                        <span class="fw-medium">{{ $userData->telepon ?? '-' }}</span>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Tanggal Lahir</small>
                        {{-- <span class="fw-medium">{{ $mahasiswa->tanggal_lahir ? \Carbon\Carbon::parse($mahasiswa->tanggal_lahir)->isoFormat('D MMMM Y') : '-' }}</span> --}}
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="bg-light rounded p-3">
                        <small class="text d-block mb-1">Asal Kota</small>
                        <span class="fw-medium">{{ $userData->asal_kota ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Statistik Ringkas --}}
            <div class="row g-3 pt-3 border-top">
                <div class="col-4">
                    <div class="text-center bg-label-primary rounded p-3">
                        {{-- <h4 class="mb-0 text-primary">{{ number_format($rataIPK, 2) }}</h4> --}}
                        <small class="text-primary">IPK Terakhir</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center bg-label-success rounded p-3">
                        {{-- <h4 class="mb-0 text-success">{{ $totalSKS ?? '0' }}</h4> --}}
                        <small class="text-success">SKS Lulus</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-center bg-label-warning rounded p-3">
                        {{-- <h4 class="mb-0 text-warning">{{ $totalwali }}</h4> --}}
                        <small class="text-warning">Semester Aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection