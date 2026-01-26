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
                            <th class="text-truncate">No</th>
                            <th class="text-truncate">Tanggal</th>
                            <th class="text-truncate">Nama</th>
                            <th class="text-truncate">IPK</th>
                            <th class="text-truncate">Keluhan</th>
                            <th class="text-truncate">Masukan</th>
                            <th class="text-truncate">KHS</th>
                            <th class="text-truncate">Status</th>
                            <th class="text-truncate">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perwaliandosen as $index => $historydosenwali)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ optional($historydosenwali->created_at)->translatedFormat('d F Y') ?? '-' }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <h6 class="mb-0 text-truncate">
                                                {{ $historydosenwali->student->name }}
                                            </h6>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-truncate">
                                    {{ $historydosenwali->ipk }}
                                </td>
                                <td class="text-truncate">
                                    {{ $historydosenwali->keluhan }}
                                </td>
                                <td class="text-truncate">
                                    {{ $historydosenwali->masukan }}
                                </td>
                                <td class="text-truncate">
                                    <a href="{{ asset('storage/' . $historydosenwali->khs) }}" target="_blank">Lihat file</a>
                                </td>

                                <td>
                                    @if ($historydosenwali->status === 'pending')
                                        <span class="badge bg-label-warning rounded-pill">Pending</span>
                                    @else
                                        <span class="badge bg-label-success rounded-pill">Done</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($historydosenwali->status === 'pending')
                                        <a href="{{ route('perwalian.edit', $historydosenwali->id) }}"
                                            class="btn btn-icon btn-sm btn-outline-primary" title="Edit">
                                            <i class="ri-edit-2-fill"></i>
                                        </a>
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
    @include('_partials.alert')
@endsection
