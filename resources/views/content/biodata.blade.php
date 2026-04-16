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
                    <small class="text-muted">NIM: {{ $userData['data']['student_detail']['nim'] ?? '_' }}</small>
                </div>
                <span class="badge bg-label-success">{{ $userData['data']['status'] }}</span>
            </div>

            <div class="card-body">
                {{-- Informasi Akademik --}}
                <p class="text-uppercase fw-semibold text small mb-2">Informasi Akademik</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="bg-light rounded p-3">
                            <small class="text d-block mb-1">Program Studi</small>
                            <span
                                class="fw-medium">{{ $userData['data']['student_detail']['study_program_name'] ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="bg-light rounded p-3">
                            <small class="text d-block mb-1">Jurusan</small>
                            <span class="fw-medium">{{ $userData['data']['student_detail']['major_name'] ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="bg-light rounded p-3">
                            <small class="text d-block mb-1">Dosen Wali</small>
                            <span class="fw-medium">{{ $userData['data']['student_detail']['supervisor_lectures'][0]['name'] ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="bg-light rounded p-3">
                            <small class="text d-block mb-1">Tahun Masuk</small>
                            <span class="fw-medium">{{ $userData['data']['student_detail']['generation'] ?? '-' }}</span>
                        </div>
                    </div>
                    {{-- Informasi Pribadi --}}
                    <p class="text-uppercase fw-semibold text small mb-2">Informasi Pribadi</p>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="bg-light rounded p-3">
                                <small class="text d-block mb-1">Email</small>
                                <span class="fw-medium">{{ Auth::user()->email }}</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="bg-light rounded p-3">
                                <small class="text d-block mb-1">Tanggal Lahir</small>
                                <span class="fw-medium">{{ $userData['data']['birth_place'] ?? '-' }},
                                    {{ $userData['data']['birth_date'] ?? '-' }} </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
