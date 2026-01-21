

@section('content')
<div class="row gy-6">
  <!-- Total Perwalian -->

  <div class="col-lg-12">
    <div class="card h-80">
      <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Total Perwalian</h5>
          <div class="dropdown">
          </div>
        </div>
      <div class="card-body pt-lg-12">
        <div class="row g-12">
          <div class="col-md-4 col-8">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-primary rounded shadow-xs">
                  <i class="ri-group-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-6">
                <p class="mb-0">Total Mahasiswa Wali</p>
                <h3 class="mb-0">{{ $totalperwalian['totalMahasiswa'] }}</h3>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-3">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-warning rounded shadow-xs">
                  <i class="ri-group-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-4">
                <p class="mb-0">Total Perwalian Pending</p>
                <h3 class="mb-0">{{ $totalperwalian['totalPending'] }}</h3>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-success rounded shadow-xs">
                  <i class="ri-group-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-4">
                <p class="mb-0">Total Perwalian Selesai</p>
                <h3 class="mb-0">{{ $totalperwalian['totalDone'] }}</h3>
              </div>
            </div>
          </div>
          <a href="javascript:;" class="btn btn-sm btn-primary">Detail</a>
        </div>
        </div>

      </div>
    </div>
  </div>
  <!--/ Transactions -->

  <!-- Overview -->
  <div class="col-xl-12 col-md-6">
  <div class="card">
    <div class="card-header">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-1">10 IPK Tertinggi Mahasiswa Wali</h5>

        <form method="GET">
          <select name="semester" class="form-select form-select-sm"
                  onchange="this.form.submit()">
            <option value="">Semua Semester</option>
            @foreach ($listSemester as $smt)
              <option value="{{ $smt }}"
                {{ $semester == $smt ? 'selected' : '' }}>
                Semester {{ $smt }}
              </option>
            @endforeach
          </select>
        </form>
      </div>
    </div>

    <div class="card-body pt-lg-2">
      <div id="ipkTopChart"></div>
    </div>
  </div>
</div>

<script>
  window.ipkTopData = @json($ipkTopData);
</script>


@endsection
