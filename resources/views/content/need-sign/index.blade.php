@extends('layouts.contentNavbarLayout')

@section('title', 'Dokumen Perwalian Menunggu Tanda Tangan Kajur')

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
            <li class="breadcrumb-item active">Dokumen Perwalian</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="row g-2 align-items-center my-3 mx-1">
                    <div class="col-12 col-md">
                        <form action="{{ route('page.need_sign') }}" method="GET" id="form-filter">
                            <div class="row g-2 justify-content-md-end">
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="input-group input-group-sm">
                                        <input type="search" class="form-control" placeholder="Cari berdasarkan nama"
                                            id="search" name="search" value="{{ request('search') }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-truncate">No</th>
                                <th class="text-truncate">Nama</th>
                                <th class="text-truncate">NIM</th>
                                <th class="text-truncate">Program Studi</th>
                                <th class="text-truncate">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($needSigns as $index => $needSign)
                                <tr>
                                    <td>{{ $needSigns->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0 text-truncate">{{ $needSign->student->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">{{ explode('@', $needSign->student->email)[0] }}</td>
                                    <td class="text-truncate">
                                        <div class="d-flex align-items-center">
                                            <span>{{ $needSign->student->study_program }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($needSign->khs)
                                            <form action="{{ route('page.need_sign.sign', $needSign->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-icon btn-sm btn-outline-primary"
                                                    title="Tanda Tangani">
                                                    <i class="ri-check-line"></i>
                                                </button>
                                            </form>

                                            <a class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ \App\Helpers\FileHelper::get("khs_files/{$needSign->khs}") }}"
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
                                    <td colspan="5" class="text-center">
                                        Tidak ada dokumen yang menunggu tanda tangan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $needSigns->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
