@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Perwalian')

@include('_partials.alert')

@section('content')
<div class="row g-4">
  

  <!-- Form Edit -->
  <div class="col-12 col-xl-6">
    <div class="card mb-8">
      <div class="card-header">
        <div class="row">
            <h5>Data Perwalian</h5>
      </div >
      <div>
        <form action="{{ route('perwalian.nonkhs.update', $perwalian->id) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="mb-4">
            <label class="form-label">Nama Mahasiswa</label>
            <input type="text"
                   class="form-control"
                   value="{{ $perwalian->student->name }}"
                   readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Keluhan</label>
            <textarea class="form-control"
                      rows="3"
                      readonly>{{ $perwalian->keluhan }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Masukan Dosen Wali</label>
            <textarea name="masukan"
                      class="form-control"
                      rows="3" required>{{ old('masukan', $perwalian->masukan) }}</textarea>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('dataperwaliandosen') }}" class="btn btn-secondary">
              Kembali
            </a>
            <button type="submit" class="btn btn-primary">
              Diterima
            </button>
          </div>

        </form>
        </div>
        

    </div>
  </div>

</div>
@include('_partials.alert')
@endsection
