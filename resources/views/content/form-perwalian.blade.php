@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

<script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
<link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />

@section('content')
<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Silakan Upload Kartu Hasil Studi</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('upload.khs') }}" method="POST" class="dropzone" id="file-upload" enctype="multipart/form-data">
            @csrf
            <div class="dz-message">
              <h3>Klik untuk Upload KHS</h3>
            </div>
          {{-- <div class="form-floating form-floating-outline mb-6">
            <textarea id="basic-default-message" class="form-control" placeholder="Hi, Do you have a moment to talk Joe?" style="height: 60px;"></textarea>
            <label for="basic-default-message">Message</label>
          </div> --}}
        </form>
      </div>
    </div>
  </div>
<!-- Merged -->
  <div class="col-xl">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Perwalian</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('perwalian.khs') }}" method="POST">
            @csrf
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ri-user-line ri-20px"></i></span>
              <input type="text" class="form-control" id="basic-icon-default-fullname" name="nama_lengkap" value="{{ Auth::user()->name }}" readonly/>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-company2" class="input-group-text"><i class="ri-building-4-line ri-20px"></i></span>
              <input type="text" id="basic-icon-default-company" class="form-control" value="{{ $semesterAktif }}" readonly/>
          </div>
          <div class="mb-6">
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="ri-building-line ri-20px"></i></span>
                <input type="text" id="basic-icon-default-company2" class="form-control" value="{{ $programStudi }}" readonly/>
              <span id="basic-icon-default-company2" class="input-group-text"></span>
            </div>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-company2" class="input-group-text"><i class="ri-building-4-line ri-20px"></i></span>
              <input type="text" id="basic-icon-default-company" class="form-control" placeholder="IPK"/>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-message2" class="input-group-text"><i class="ri-chat-4-line ri-20px"></i></span>
              <textarea id="basic-icon-default-message" class="form-control" placeholder="Keluhan" style="height: 60px;"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    new Dropzone("#file-upload", {
        maxFilesize: 2,
        acceptedFiles: ".pdf",
        success: function(file, response) {
            console.log(response);
        },
        error: function(file, response) {
            console.log(response);
        }
    });
</script>
@endsection
