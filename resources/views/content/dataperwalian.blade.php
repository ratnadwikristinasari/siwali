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
<h4>Data Perwalian</h4>
<div class="col-12">
    <div class="card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th class="text-truncate">Nama</th>
              <th class="text-truncate">Program Studi</th>
              <th class="text-truncate">IPK</th>
              <th class="text-truncate">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($perwalian as $item)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{ asset('assets/img/avatars/1.png') }}" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">
                      {{ $item->student->name ?? '-' }}
                    </h6>
                  </div>
                </div>
              </td>

              <td class="text-truncate">
                {{ Auth::user()->email ?? '-' }}
              </td>

              <td class="text-truncate">
                {{ $item->ipk ?? '-' }}
              </td>

              <td>
                @if ($item->status === 'pending')
                  <span class="badge bg-label-warning rounded-pill">Pending</span>
                @else
                  <span class="badge bg-label-success rounded-pill">Done</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center">
                Belum ada data perwalian
              </td>
            </tr>
            @endforelse
            </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
@endsection
