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

            $('#type, #status').on('change', function() {
                $('#form-filter').submit();
            });
        });
    </script>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card overflow-hidden">
            <div class="row g-2 align-items-center my-3 mx-1">
                <div class="col-12 col-md">
                    <form
                        action="{{ request()->route()->getName() === 'dataperwalian' ? route('dataperwalian') : route('detailmahasiswa', $id) }}"
                        method="GET" id="form-filter">
                        <div class="row g-2 justify-content-md-end">
                            {{-- filter type --}}
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="input-group input-group-sm">
                                    <select class="form-select" name="type" id="type">
                                        <option value="">Semua Jenis</option>
                                        <option value="gpa_advising"
                                            {{ request('type') == 'gpa_advising' ? 'selected' : '' }}>
                                            Perwalian KHS
                                        </option>
                                        <option value="non_gpa_advising"
                                            {{ request('type') == 'non_gpa_advising' ? 'selected' : '' }}>
                                            Konseling
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="input-group input-group-sm">
                                    <select class="form-select" name="status" id="status">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Menunggu Tanda Tangan Dosen Wali
                                        </option>
                                        <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>
                                            Menunggu Tanda Tangan Kajur
                                        </option>
                                        <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                    </select>
                                </div>
                            </div>
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
                                        Konseling
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
                                        <span class="badge bg-label-warning rounded-pill">Menunggu Tanda Tangan Dosen Wali</span>
                                    @elseif ($historywali->status === 'signed')
                                        <span class="badge bg-label-info rounded-pill">Menunggu Tanda Tangan
                                            Kajur</span>
                                    @else
                                        <span class="badge bg-label-success rounded-pill">Selesai</span>
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
