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
@section('vendor-pagination')
@vite('resources/views/vendor/pagination/bootstrap-page.blade.php')
@endsection

@section('content')
<h4>Data Dosen</h4>
<div class="col-12">
    <div class="card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th class="text-truncate">Nama</th>
              <th class="text-truncate">NIP</th>
              <th class="text-truncate">Home Base</th>
              <th class="text-truncate">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($dosens as $listdosen)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">{{ $listdosen['name'] }}</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">{{ $listdosen['nip'] }}</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-user-3-line ri-22px text-success me-2"></i>
                  <span>{{ $listdosen['position'] }}</span>
                </div>
              </td>
              <td><span class="badge bg-label-secondary rounded-pill">Inactive</span></td>
            @endforeach

          </tbody>
        </table>
        {{ $dosens->links('pagination::bootstrap-page') }}
      </div>
    </div>

  </div>
@endsection
