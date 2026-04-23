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

    @include('content.biodata.biodata')
@endsection
