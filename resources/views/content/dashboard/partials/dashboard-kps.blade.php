

@section('content')
<div class="row gy-6">
  <!-- Transactions -->
  <div class="col-lg-12">
    <div class="card h-100">
      <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Data Teknik Informatika</h5>
        </div>
      </div>
      <div class="card-body pt-lg-10">
        <div class="row g-6">
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-primary rounded shadow-xs">
                  <i class="ri-pie-chart-2-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <p class="mb-0">Dosen</p>
                <h5 class="mb-0">24</h5>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-success rounded shadow-xs">
                  <i class="ri-group-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <p class="mb-0">Mahasiswa</p>
                <h5 class="mb-0">300</h5>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-warning rounded shadow-xs">
                  <i class="ri-macbook-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <p class="mb-0">Belum Perwalian</p>
                <h5 class="mb-0">201</h5>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-info rounded shadow-xs">
                  <i class="ri-money-dollar-circle-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-3">
                <p class="mb-0">Selesai Perwalian</p>
                <h5 class="mb-0">99</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Transactions -->

  <!-- Weekly Overview Chart -->
  <div class="col-xl-12 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">10 IPK Tertinggi Program Studi Teknik Informatika</h5>
          <div class="dropdown">
            <button class="btn text-muted p-0" type="button" id="weeklyOverviewDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="ri-more-2-line ri-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOverviewDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body pt-lg-2">
        <div id="weeklyOverviewChart"></div>
          <div class="d-grid mt-3 mt-md-4">
            <button class="btn btn-primary" type="button">Details</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Weekly Overview Chart -->
</div>
@endsection
