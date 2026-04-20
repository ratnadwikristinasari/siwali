@extends('layouts.contentNavbarLayout')

@section('title', 'Edit Perwalian')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dataperwaliandosen') }}">Data Perwalian Dosen</a>
            </li>
            <li class="breadcrumb-item active">Detail Perwalian</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Form Edit -->
        <div class="col-12=">
            <div class="card mb-8">
                <div class="card-header">
                    <div class="row">
                        <h5>Data Perwalian</h5>
                    </div>
                    <div>
                        <form action="{{ route('perwalian.update', $perwalian->id) }}" method="POST" id="perwalian.update">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label">Nama Mahasiswa</label>
                                <input type="text" class="form-control" value="{{ $perwalian->student->name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keluhan</label>
                                <textarea class="form-control" rows="3" readonly>{{ $perwalian->keluhan }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Masukan Dosen Wali</label>
                                <textarea name="masukan" placeholder="Masukkan masukan Anda Minimal 10 Karakter" minlength="10" class="form-control @error('masukan') is-invalid @enderror" rows="3" required>{{ old('masukan', $perwalian->masukan) }}</textarea>
                                @error('masukan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dataperwaliandosen') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                                @if ($perwalian->status === 'pending')
                                    <button type="button" class="btn btn-primary" id="btn-submit-terima">
                                        Terima Perwalian
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('_partials.alert')
@endsection
