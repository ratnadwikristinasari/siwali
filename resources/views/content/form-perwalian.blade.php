@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('page-style')
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Silakan Upload Kartu Hasil Studi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('upload.khs') }}" method="POST" class="dropzone" id="file-upload"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="dz-message">
                            <h3>Klik untuk Upload KHS</h3>
                        </div>
                        <div id="upload-status" class="mt-2"></div>
                    </form>
                    {{-- preview KHS --}}
                    <div id="khs-preview" class="mt-4 d-none">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Preview Kartu Hasil Studi</h5>
                            </div>
                            <div class="card-body p-0">
                                <iframe id="khs-iframe" src="" width="100%" height="600" style="border:none;">
                                </iframe>
                            </div>
                        </div>
                    </div>
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
                            <span id="basic-icon-default-company2" class="input-group-text"><i
                                    class="ri-numbers-line"></i></span>
                            <input type="text" id="basic-icon-default-company" class="form-control" name="ipk"
                                placeholder="IPK" required />
                        </div>
                        <div class="input-group input-group-merge mb-6">
                            <span id="basic-icon-default-message2" class="input-group-text"><i
                                    class="ri-sticky-note-line"></i></span>
                            <textarea id="basic-icon-default-message" class="form-control" name="keluhan" style="height: 60px;"
                                placeholder="Keterangan" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                            Ajukan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    @endsection
    @section('page-script')
        <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
        <script type="text/javascript">
            new Dropzone("#file-upload", {
                paramName: "file",
                maxFiles: 1,
                acceptedFiles: ".pdf",
                addRemoveLinks: true,

                success: function(file, response) {
                    document.getElementById('upload-status').innerHTML =
                        `<span class="text-success">KHS berhasil diupload</span>`;

                    document.getElementById('btn-submit').disabled = false;

                    document.getElementById('khs-preview').classList.remove('d-none');
                    document.getElementById('khs-iframe').src = response.url;

                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Berhasil',
                        text: 'KHS berhasil diupload',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },

                error: function() {
                    document.getElementById('upload-status').innerHTML =
                        `<span class="text-danger">Format KHS harus PDF</span>`;
                },

                removedfile: function(file) {
                    document.getElementById('upload-status').innerHTML = '';
                    document.getElementById('btn-submit').disabled = true;
                    document.getElementById('khs-preview').classList.add('d-none');
                    document.getElementById('khs-iframe').src = '';
                    file.previewElement.remove();
                },
            });
        </script>
    @endsection
