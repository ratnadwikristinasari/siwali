@extends('layouts.contentNavbarLayout')

@section('title', ' Form Perwalian')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-style1">
            <li class="breadcrumb-item">
                <a href="{{ route('content.dashboard.dashboard-main') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Form Perwalian</li>
        </ol>
    </nav>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Perwalian</h5>
                </div>
                <div class="card-body">
                    <form id="formPerwalian" action="{{ route('perwalian.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="semester" id="semester">
                        <label for="type">Jenis Perwalian</label>
                        <div class="mb-3">
                            <select name="type" id="type" class="form-select form-control" required>
                                <option value="" selected>Pilih Jenis Perwalian</option>
                                <option value="gpa_advising">Perwalian KHS</option>
                                <option value="non_gpa_advising">Perwalian Non KHS</option>
                            </select>
                        </div>

                        <label for="name">Nama Lengkap</label>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="basic-icon-default-fullname" name="nama_lengkap"
                                value="{{ Auth::user()->name }}" readonly />
                        </div>

                        <label for="semester_id">Semester</label>
                        <div class="mb-3">
                            <select name="semester_id" id="semester_id" class="form-select form-control" required>
                                <option value="" selected>Pilih Semester</option>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem['semester_id'] }}">
                                        {{ $sem['semester'] }} {{ $sem['is_active'] ? '(Semester Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="semester-warning" class="text-danger" style="display: none;">Anda sudah menyelesaikan
                                Perwalian KHS untuk semester ini.</small>
                        </div>
                        <label for="name">Program Studi</label>
                        <div class="mb-3">
                            <input type="text" id="basic-icon-default-company2" class="form-control"
                                value="{{ $programStudi }}" readonly />
                        </div>
                        <div id="ipk-container" style="display: none;">
                            <label for="ipk" id="label-ipk">IPK Semester</label>
                            <div class="mb-3">
                                <input type="text" id="ipk" class="form-control" name="ipk" placeholder="IPK"
                                    readonly />
                            </div>
                        </div>

                        <label for="name">Catatan atau keluhan</label>
                        <div class="mb-3">
                            <textarea id="basic-icon-default-message" class="form-control @error('keluhan') is-invalid @enderror" name="keluhan"
                                style="height: 100px;" required></textarea>
                            @error('keluhan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            Ajukan
                        </button>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Lempar data dari Controller ke JavaScript
                            const khsDoneStatuses = @json($khsDoneStatuses);
                            const gpaData = @json($gpaData);

                            const typeSelect = document.getElementById('type');
                            const semesterSelect = document.getElementById('semester_id');
                            const ipkContainer = document.getElementById('ipk-container');
                            const ipkInput = document.getElementById('ipk');
                            const labelIpk = document.getElementById('label-ipk');
                            const semesterWarning = document.getElementById('semester-warning');
                            const semesterHiddenInput = document.getElementById('semester');

                            function updateFormBehavior() {
                                const selectedType = typeSelect.value;
                                const selectedSemId = semesterSelect.value;

                                // 1. Tampilkan IPK jika memilih Perwalian KHS
                                if (selectedType === 'gpa_advising') {
                                    ipkContainer.style.display = 'block';
                                    ipkInput.disabled = false;
                                } else {
                                    ipkContainer.style.display = 'none';
                                    ipkInput.disabled = true;
                                }

                                // 2. Validasi status "Done" berdasarkan semester yang dipilih
                                Array.from(semesterSelect.options).forEach(option => {
                                    if (option.value === "") return; // Skip opsi default

                                    // Disable opsi semester JIKA: tipe perwalian = KHS DAN status khsDone = true
                                    if (selectedType === 'gpa_advising' && khsDoneStatuses[option.value]) {
                                        option.disabled = true;
                                        // Tambahkan teks penanda untuk UX yang lebih baik
                                        if (!option.text.includes('(Selesai)')) {
                                            option.text += ' (Selesai)';
                                        }
                                    } else {
                                        option.disabled = false;
                                        // Bersihkan teks penanda
                                        option.text = option.text.replace(' (Selesai)', '');
                                    }
                                });

                                // Jika user merubah "Jenis" dan semester yang sedang dipilih ternyata di-disable, reset pilihannya
                                if (selectedSemId && semesterSelect.options[semesterSelect.selectedIndex].disabled) {
                                    semesterSelect.value = "";
                                    ipkInput.value = "";
                                    semesterHiddenInput.value = "";
                                    semesterWarning.style.display = 'block';
                                } else {
                                    semesterWarning.style.display = 'none';
                                }

                                // 3. Update Nilai IPK secara dinamis saat dropdown semester diubah
                                if (semesterSelect.value && gpaData[semesterSelect.value] !== undefined) {
                                    ipkInput.value = gpaData[semesterSelect.value];
                                    let semText = semesterSelect.options[semesterSelect.selectedIndex].text;
                                    // Bersihkan label dari teks tambahan agar rapi
                                    semText = semText.replace(' (Semester Aktif)', '').replace(' (Selesai)', '').trim();
                                    labelIpk.innerText = 'IPK Semester ' + semText;

                                    semesterHiddenInput.value = semText;
                                } else {
                                    ipkInput.value = '';
                                    labelIpk.innerText = 'IPK Semester';
                                }

                                semesterHiddenInput.value = semesterSelect.options[semesterSelect.selectedIndex].text.replace(' (Semester Aktif)', '').replace(' (Selesai)', '').trim();
                            }

                            // Trigger fungsi saat ada perubahan di dropdown Type atau Semester
                            typeSelect.addEventListener('change', updateFormBehavior);
                            semesterSelect.addEventListener('change', updateFormBehavior);

                            // Panggil sekali untuk menyesuaikan state form saat halaman dimuat
                            updateFormBehavior();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    </div>
@include('_partials.alert')
@endsection
