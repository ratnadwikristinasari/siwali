

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
                <h3 class="mb-0">15</h3>
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
                <h3 class="mb-0">15</h3>
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
                <h3 class="mb-0">5</h3>
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
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">10 IPK Tertinggi Mahasiswa Wali</h5>
          <div class="dropdown">
            <button class="btn text-muted p-0" type="button" id="weeklyOverviewDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="ri-more-2-line ri-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOverviewDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body pt-lg-2">
        <div id="weeklyOverviewChart"></div>
        <div class="mt-1 mt-md-3">
          <div class="d-flex align-items-center gap-4">
          </div>
          <div class="d-grid mt-3 mt-md-4">
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
