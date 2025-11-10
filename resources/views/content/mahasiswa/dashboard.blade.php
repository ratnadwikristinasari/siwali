@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
<div class="row gy-6">
  <!-- Congratulations card -->
  <div class="col-md-12 col-lg-7">
    <div class="card">
      <div class="card-body text-nowrap">
        <h5 class="card-title mb-0 flex-wrap text-nowrap">Congratulations Nama Mahasiswa</h5>
        <p class="mb-2">Rata - Rata IPK</p>
        <h4 class="text-primary mb-0">3.85</h4>
        <p class="mb-2">78% of target 🚀</p>
        <a href="javascript:;" class="btn btn-sm btn-primary">Detail</a>
      </div>
      <img src="{{asset('assets/img/illustrations/trophy.png')}}" class="position-absolute bottom-0 end-0 me-8 mb-8" width="83" alt="view sales">
    </div>
  </div>
  <!--/ Congratulations card -->

  <!-- Total Perwalian -->
  <div class="col-lg-5">
    <div class="card h-74">
      <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Total Perwalian</h5>
          <div class="dropdown">
          </div>
        </div>
      <div class="card-body pt-lg-10">
        <div class="row g-6">
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-primary rounded shadow-xs">
                  <i class="ri-pie-chart-2-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-4">
                <p class="mb-0">Kartu Hasil Study</p>
                <h5 class="mb-0">2</h5>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-success rounded shadow-xs">
                  <i class="ri-group-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-4">
                <p class="mb-0">Non Kartu Hasil Study</p>
                <h5 class="mb-0">5</h5>
              </div>
            </div>
          </div>
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
          <h5 class="mb-1">Grafik Pencapaian</h5>
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
