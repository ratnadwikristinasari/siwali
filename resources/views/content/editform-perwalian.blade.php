@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Perwalian')

@section('content')
<div class="row g-4">
  <div class="col-12 col-xl-6">
    <div class="card mb-4">
      <div class="card-header">
        <div class="col-xl-6">
          <h5>Kartu Hasil Studi</h5>
      </div>
            @if($perwalian->khs)
          <div class="card mt-3">
              <div class="card-body p-0">
                  <iframe
                      src="{{ asset('storage/' . $perwalian->khs) }}"
                      width="100%"
                      height="600"
                      style="border:none;">
                  </iframe>
              </div>
          </div>
      @else
          <div class="alert alert-warning mt-3">
              KHS belum diupload
          </div>
      @endif
        </div>
        

    </div>
  </div>

  <!-- Form Edit -->
  <div class="col-12 col-xl-6">
    <div class="card mb-8">
      <div class="card-header">
        <div class="row">
            <h5>Data Perwalian</h5>
      </div >
      <div>
        <form action="{{ route('perwalian.update', $perwalian->id) }}" method="POST">
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
            <label class="form-label">IPK</label>
            <input type="text"
                   class="form-control"
                   value="{{ $perwalian->ipk }}"
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
                      rows="3">{{ old('masukan', $perwalian->masukan) }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="pending" {{ $perwalian->status == 'pending' ? 'selected' : '' }}>
                Pending
              </option>
              <option value="done" {{ $perwalian->status == 'done' ? 'selected' : '' }}>
                Done
              </option>
            </select>
          </div>

          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('dataperwalian') }}" class="btn btn-secondary">
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
@endsection
