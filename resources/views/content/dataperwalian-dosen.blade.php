@extends('layouts.contentNavbarLayout')

@section('title', 'Data Perwalian Dosen')

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
            <li class="breadcrumb-item active">Data Perwalian Dosen</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="row g-2 align-items-center my-3 mx-1">
                    <div class="col-12 col-md">
                        <form action="{{ route('dataperwaliandosen') }}" method="GET" id="form-filter">
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
                                <th class="text-truncate">Nama</th>
                                <th class="text-truncate">IPK</th>
                                <th class="text-truncate">Keluhan</th>
                                <th class="text-truncate">Masukan</th>
                                <th class="text-truncate">Status</th>
                                <th class="text-truncate">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($perwaliandosen as $index => $historydosenwali)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if ($historydosenwali->type === 'gpa_advising')
                                            Perwalian KHS
                                        @else
                                            Perwalian Non KHS
                                        @endif
                                    </td>
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

                                    <td>
                                        @if ($historydosenwali->status === 'pending')
                                            <span class="badge bg-label-warning rounded-pill">Pending</span>
                                        @elseif ($historydosenwali->status === 'signed')
                                            <span class="badge bg-label-info rounded-pill">Signed</span>
                                        @else
                                            <span class="badge bg-label-success rounded-pill">Done</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('perwalian.detail', $historydosenwali->id) }}"
                                            class="btn btn-icon btn-sm btn-outline-primary" title="Detail">
                                            <i class="ri-folder-info-fill"></i>
                                        </a>
                                        @if ($historydosenwali->khs)
                                            <a class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ \App\Helpers\FileHelper::get("khs_files/{$historydosenwali->khs}") }}"
                                                title="Lihat KHS" target="_blank" rel="noopener">
                                                <i class="ri-eye-fill"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        Belum ada data perwalian
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $perwaliandosen->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
