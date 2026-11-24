@extends('layouts/contentNavbarLayout')

@section('title', 'Atur Pembimbing')

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('supervisorModal');
            const modal = new bootstrap.Modal(modalEl);
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const base = '{{ url('admin/supervisors') }}';
            const selects = modalEl.querySelectorAll('select[data-position]');
            const alertBox = document.getElementById('supervisor-alert');
            let studentId = null;
            let initial = {};

            let debounce;
            document.getElementById('search').addEventListener('keyup', function() {
                clearTimeout(debounce);
                debounce = setTimeout(() => document.getElementById('form-filter').submit(), 1000);
            });

            document.querySelectorAll('.btn-supervisor').forEach(btn => btn.addEventListener('click', async function() {
                studentId = this.dataset.id;
                document.getElementById('supervisor-student-name').textContent = this.dataset.name;
                alertBox.classList.add('d-none');
                selects.forEach(s => s.value = '');
                modal.show();

                const res = await fetch(`${base}/${studentId}`, { headers: { Accept: 'application/json' } });
                const json = await res.json();
                initial = {};
                selects.forEach(s => {
                    const item = (json.data || []).find(d => d.position.toLowerCase() === s.dataset.position);
                    s.value = item ? item.employee_id : '';
                    initial[s.dataset.position] = s.value;
                    s.nextElementSibling.textContent = item && item.is_manual ?
                        'Diatur manual (tidak ditimpa sinkronisasi Polije)' : '';
                });
            }));

            document.getElementById('supervisor-save').addEventListener('click', async function() {
                const payload = {};
                selects.forEach(s => {
                    if (s.value !== initial[s.dataset.position]) payload[s.dataset.position] = s.value || null;
                });
                if (!Object.keys(payload).length) return modal.hide();

                this.disabled = true;
                const res = await fetch(`${base}/${studentId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(payload),
                });
                const json = await res.json().catch(() => ({}));
                this.disabled = false;

                if (res.ok) return window.location.reload();
                alertBox.textContent = json.message || 'Gagal menyimpan';
                alertBox.classList.remove('d-none');
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
            <li class="breadcrumb-item active">Atur Pembimbing</li>
        </ol>
    </nav>
    <div class="card overflow-hidden">
        <div class="row g-2 align-items-center my-3 mx-1">
            <form action="{{ route('admin.supervisors') }}" method="GET" id="form-filter" class="col-12 col-md-4 ms-md-auto">
                <input type="search" class="form-control form-control-sm" placeholder="Cari nama / NIM..." id="search"
                    name="search" value="{{ request('search') }}">
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Program Studi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $index => $student)
                        <tr>
                            <td>{{ $students->firstItem() + $index }}</td>
                            <td>{{ $student['name'] }}</td>
                            <td>{{ $student['nim'] }}</td>
                            <td>{{ $student['study_program']['name'] ?? '-' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-primary btn-supervisor"
                                    data-id="{{ $student['id'] }}" data-name="{{ $student['name'] }}">
                                    Atur Pembimbing
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $students->links('pagination::bootstrap-page') }}
        </div>
    </div>

    <div class="modal fade" id="supervisorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atur Pembimbing - <span id="supervisor-student-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="supervisor-alert" class="alert alert-danger d-none"></div>
                    @foreach ($positions as $key => $label)
                        <div class="mb-3">
                            <label class="form-label">{{ $label }}</label>
                            <select class="form-select" data-position="{{ $key }}">
                                <option value="">- Kosong -</option>
                                @foreach ($lecturers as $lecturer)
                                    <option value="{{ $lecturer['value'] }}">{{ $lecturer['label'] }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted"></small>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="supervisor-save">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
