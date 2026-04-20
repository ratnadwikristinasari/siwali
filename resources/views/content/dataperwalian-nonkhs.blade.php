@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

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
              <th class="text-truncate">No</th>
              <th class="text-truncate">Tanggal</th>
              <th class="text-truncate">IPK</th>
              <th class="text-truncate">Keterangan</th>
              <th class="text-truncate">Masukan</th>
              <th class="text-truncate">KHS</th>
              <th class="text-truncate">Status</th>
            </tr>
          </thead>
          <tbody>
           @forelse ($perwalian as $index => $historywali)
<tr>
  <td>{{ $index + 1 }}</td>

  {{-- TANGGAL --}}
  <td>
    {{ optional($historywali->created_at)->translatedFormat('d F Y') ?? '-' }}
  </td>

  {{-- IPK --}}
  <td class="text-truncate">
    {{ $historywali->ipk}}
  </td>
  <td class="text-truncate">
    {{ $historywali->keluhan}}
  </td>
  <td class="text-truncate">
    {{ $historywali->masukan}}
  </td>
  <td class="text-truncate">
    <a href="{{ asset('storage/'.$historywali->khs)}}" target="_blank">Lihat file</a>
  </td>

  {{-- STATUS --}}
  <td>
    @if ($historywali->status === 'pending')
      <span class="badge bg-label-warning rounded-pill">Pending</span>
    @else
      <span class="badge bg-label-success rounded-pill">Done</span>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="8" class="text-center">
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
