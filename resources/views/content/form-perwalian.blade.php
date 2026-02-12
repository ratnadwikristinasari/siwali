@extends('layouts.contentNavbarLayout')

@section('title', ' Form Perwalian')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Form Perwalian</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Perwalian</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perwalian.store') }}" method="POST">
                        @csrf
                        <label for="type">Jenis Perwalian</label>
                        <div class="mb-3">
                            <select name="type" id="type" class="form-select form-control" required>
                                <option value="" selected>Pilih Jenis Perwalian</option>
                                <option value="gpa_advising" {{ $hasAdvising ? 'disabled' : '' }}>Perwalian KHS
                                    {{ $hasAdvising ? '(Anda sudah perwalian KHS semester ini)' : '' }}</option>
                                <option value="non_gpa_advising">Perwalian Non KHS
                                </option>
                            </select>
                        </div>

                        <label for="name">Nama Lengkap</label>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="basic-icon-default-fullname" name="nama_lengkap"
                                value="{{ Auth::user()->name }}" readonly />
                        </div>

                        <label for="name">Semester</label>
                        <div class="mb-3">
                            <input type="text" id="basic-icon-default-company" class="form-control"
                                value="{{ $kelas }}" readonly />
                        </div>
                        <label for="name">Program Studi</label>
                        <div class="mb-3">
                            <input type="text" id="basic-icon-default-company2" class="form-control"
                                value="{{ $programStudi }}" readonly />
                        </div>
                        <label for="name">IPK Semester {{ $kelas }}</label>
                        <div class="mb-3">
                            <input type="text" id="basic-icon-default-company" class="form-control" name="ipk"
                                placeholder="IPK" readonly value="{{ $currentGPA }}" />
                        </div>
                        <label for="name">Catatan atau keluhan</label>
                        <div class="mb-3">
                            <textarea id="basic-icon-default-message" class="form-control @error('keluhan') is-invalid @enderror" name="keluhan"
                                style="height: 100px;" required></textarea>
                            @error('keluhan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            Ajukan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

@endsection
