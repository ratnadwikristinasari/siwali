@extends('layouts.contentNavbarLayout')

@section('title', 'Riwayat Perwalian')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#search').on('keyup', function() {
                let debounceTimer;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    $('#form-filter').submit();
                }, 1000);
            });
        });
    </script>
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Riwayat Perwalian</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="row g-2 align-items-center my-3 mx-1">
                    <div class="col-12 col-md">
                        <form action="{{ route('dataperwalian') }}" method="GET" id="form-filter">
                            <div class="row g-2 justify-content-md-end">
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <input type="search" class="form-control" placeholder="Cari..." id="search"
                                            name="search" value="{{ request('search') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="text-truncate">No</th>
                                <th class="text-truncate">Jenis Perwalian</th>
                                <th class="text-truncate">Tanggal</th>
                                <th class="text-truncate">IPK</th>
                                <th class="text-truncate">Keterangan</th>
                                <th class="text-truncate">Masukan</th>
                                <th class="text-truncate">Status</th>
                                <th class="text-truncate">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($perwalian as $index => $historywali)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($historywali->type === 'gpa_advising')
                                            Perwalian KHS
                                        @else
                                            Perwalian Non KHS
                                        @endif
                                    </td>
                                    <td>
                                        {{ optional($historywali->created_at)->translatedFormat('d F Y') ?? '-' }}
                                    </td>
                                    <td class="text-truncate">
                                        {{ $historywali->ipk }}
                                    </td>
                                    <td class="text-truncate">
                                        {{ $historywali->keluhan }}
                                    </td>
                                    <td class="text-truncate">
                                        {{ $historywali->masukan }}
                                    </td>
                                    <td>
                                        @if ($historywali->status === 'pending')
                                            <span class="badge bg-label-warning rounded-pill">Pending</span>
                                        @elseif ($historywali->status === 'signed')
                                            <span class="badge bg-label-info rounded-pill">Signed</span>
                                        @else
                                            <span class="badge bg-label-success rounded-pill">Done</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($historywali->type === 'gpa_advising' && $historywali->khs)
                                            <a class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ \App\Helpers\FileHelper::get("khs_files/{$historywali->khs}") }}"
                                                title="Lihat KHS" target="_blank" rel="noopener">
                                                <i class="ri-eye-fill"></i>
                                            </a>
                                        @else
                                            -
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

                {{ $perwalian->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
