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
<h4>Data Program Studi</h4>
<div class="col-12">
    <div class="card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
                <th class="text-truncate">No</th>
              <th class="text-truncate">Program Studi</th>
              <th class="text-truncate">Jurusan</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($prodis as $index => $listprodi)
            <tr>
                <td>{{ $prodis->firstItem() + $index }}</td>
              <td>
                <div class="d-flex align-items-center">
        
                  <div>
                    <h6 class="mb-0 text-truncate">{{ $listprodi['name'] }}</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">{{ $listprodi['major']['name'] }}</td>
            </tr>
            @endforeach
            
          </tbody>
        </table>
        {{ $prodis->links('pagination::bootstrap-page') }}
      </div>
    </div>
  </div>
  </div>
@endsection
