@extends('layouts.contentNavbarLayout')

@section('title', 'Dokumen Perwalian Menunggu Tanda Tangan Kajur')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = @json(session('success'));

            if (successMessage && typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: successMessage,
                    timer: 3000,
                    showConfirmButton: false
                });
            }

            $('#search').on('keyup', function() {
                let debounceTimer;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    $('#form-filter').submit();
                }, 1000);
            });
            // LOGIKA CHECK ALL & BULK ACTION
            const $checkAll = $('#check-all');
            const $checkItems = $('.check-item');
            const $btnBulkSign = $('#btn-bulk-sign');
            const $selectedCount = $('#selected-count');

            function updateBulkButton() {
                const checkedCount = $('.check-item:checked').length;
                $selectedCount.text(checkedCount);
                $btnBulkSign.prop('disabled', checkedCount === 0);
                $checkAll.prop('checked', checkedCount === $checkItems.length && $checkItems.length > 0);
            }

            $checkAll.on('change', function() {
                $checkItems.prop('checked', $(this).prop('checked'));
                updateBulkButton();
            });

            $checkItems.on('change', function() {
                updateBulkButton();
            });

            // Mencegah double submit form massal dan memunculkan efek loading
            $('#bulk-sign-form').on('submit', function(e) {
                if ($(this).data('submitted')) {
                    e.preventDefault();
                    return;
                }
                $(this).data('submitted', true);
                
                $btnBulkSign.prop('disabled', true);
                $btnBulkSign.html('<i class="ri-loader-4-line ri-spin me-1"></i> Memproses...');
            });

            $('.js-sign-form').on('submit', function(e) {
                const $form = $(this);

                if ($form.data('submitted')) {
                    e.preventDefault();
                    return;
                }

                $form.data('submitted', true);

                const $button = $form.find('.js-sign-button');
                const $icon = $button.find('.js-sign-icon');

                $button.prop('disabled', true);
                $icon.removeClass('ri-check-line').addClass('ri-loader-4-line ri-spin');
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
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <form action="{{ route('page.need_sign.sign_bulk') }}" method="POST" id="bulk-sign-form" class="m-0">
            @csrf
            <button type="submit" class="btn btn-primary" id="btn-bulk-sign" disabled>
                <i class="ri-check-double-line me-1"></i> Tanda Tangani Terpilih (<span id="selected-count">0</span>)
            </button>
        </form>
    </div>
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
                                <th style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check-all">
                                    </div>
                                </th>
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
                                    <!-- Checkbox Satuan -->
                                    <td>
                                        @if ($needSign->khs)
                                            <div class="form-check">
                                                <input class="form-check-input check-item" type="checkbox" name="ids[]" value="{{ $needSign->id }}" form="bulk-sign-form">
                                            </div>
                                        @else
                                            <!-- Disable checkbox jika tidak ada file KHS -->
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" disabled>
                                            </div>
                                        @endif
                                    </td>
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
                                            <!-- Sign Satuan -->
                                            <form action="{{ route('page.need_sign.sign', $needSign->id) }}" method="POST" style="display:inline;" class="js-sign-form">
                                                @csrf
                                                <button type="submit" class="btn btn-icon btn-sm btn-outline-primary js-sign-button" title="Tanda Tangani">
                                                    <i class="ri-check-line js-sign-icon"></i>
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
                                    <td colspan="6" class="text-center">
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
