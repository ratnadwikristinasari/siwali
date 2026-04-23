@extends('layouts/contentNavbarLayout')

@section('title', 'Detail Mahasiswa')

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
