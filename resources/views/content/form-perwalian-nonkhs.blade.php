@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('page-style')
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-12">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Konseling</h5>
                
                </div>
                <div class="card-body">
                    <form action="{{ route('perwalian.nonkhs') }}" method="POST">
                        @csrf
                        <div class="input-group input-group-merge mb-6">
                            <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                    class="ri-user-line ri-20px"></i></span>
                            <input type="text" class="form-control" id="basic-icon-default-fullname" name="nama_lengkap"
                                value="{{ Auth::user()->name }}" readonly />
                        </div>
                        <div class="input-group input-group-merge mb-6">
                            <span id="basic-icon-default-company2" class="input-group-text"><i
                                    class="ri-building-4-line ri-20px"></i></span>
                            <input type="text" id="basic-icon-default-company" class="form-control"
                                value="{{ $kelas }}" readonly />
                        </div>
                        <div class="mb-6">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ri-building-line ri-20px"></i></span>
                                <input type="text" id="basic-icon-default-company2" class="form-control"
                                    value="{{ $programStudi }}" readonly />
                                <span id="basic-icon-default-company2" class="input-group-text"></span>
                            </div>
                        </div>
                        <div class="input-group input-group-merge mb-6">
                            <span id="basic-icon-default-message2" class="input-group-text"><i
                                    class="ri-sticky-note-line"></i></span>
                            <textarea id="basic-icon-default-message" class="form-control" name="konsultasi" style="height: 60px;"
                                placeholder="Masukkan Keluhan Anda" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-submit-nonkhs" enabled>
                            Kirim
                        </button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('page-script')
    


        @include('_partials.alert-nonkhs')

    @endsection
