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
            <div id="upload-status" class="mt-2"></div>
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
        <form action="{{ route('perwalian.store') }}" method="POST">
            @csrf
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ri-user-line ri-20px"></i></span>
              <input type="text" class="form-control" id="basic-icon-default-fullname" name="nama_lengkap" value="{{ Auth::user()->name }}" readonly/>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-company2" class="input-group-text"><i class="ri-building-4-line ri-20px"></i></span>
              <input type="text" id="basic-icon-default-company" class="form-control" value="{{ $kelas }}" readonly/>
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
              <input type="text" id="basic-icon-default-company" class="form-control" name="ipk" placeholder="IPK"/>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-message2" class="input-group-text"><i class="ri-chat-4-line ri-20px"></i></span>
              <textarea id="basic-icon-default-message" class="form-control" name="keluhan" style="height: 60px;" placeholder="Keterangan"></textarea>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-message2" class="input-group-text"><i class="ri-chat-4-line ri-20px"></i></span>
              <textarea id="basic-icon-default-message" class="form-control" name="masukan" style="height: 60px;" placeholder="Masukan"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>
  </div>
</div>
<script type="text/javascript">
    new Dropzone("#file-upload", {
        maxFile: 1,
        acceptedFiles: ".pdf",
        addRemoveLink: true,
        success: function(file, response) {
          document.getElementById('upload-status').innerHTML =
          `<span class="text-success">KHS berhasil diupload</span>`;
        },
        error: function() {
           document.getElementById('upload-status').innerHTML=
            `<span class="text-success">Gagal KHS diupload</span>`;
        },
        removedfile: function(file) {
          document.getElementById('upload-status').innerHTML = '';
          file.previewElement.remove();
        }
    });
</script>
@endsection
