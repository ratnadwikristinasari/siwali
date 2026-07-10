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
                    <li>{!! $error !!}</li>
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
                                <option value="non_gpa_advising">Konseling</option>
                            </select>
                        </div>

                        <label for="name">Nama Lengkap</label>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="basic-icon-default-fullname" name="nama_lengkap"
                                value="{{ Auth::user()->name }}" readonly disabled />
                        </div>

                        <label for="semester_id">Semester</label>
                        <div class="mb-3">
                            <select name="semester_id" id="semester_id" class="form-select form-control" required>
                                <option value="" selected>Pilih Semester</option>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem['semester_id'] }}"
                                        {{ $sem['is_active'] ? 'data-active="true"' : '' }}>
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
                                value="{{ $programStudi }}" readonly disabled />
                        </div>
                        <div id="ipk-container" style="display: none;">
                            <label for="ipk" id="label-ipk">IPK Semester</label>
                            <div class="mb-3">
                                <input type="text" id="ipk" class="form-control" name="ipk" placeholder="IPK"
                                    readonly disabled />
                            </div>
                        </div>

                        <label for="name">Catatan atau keluhan</label>
                        <div class="mb-3">
                            <textarea id="basic-icon-default-message" minlength="10" class="form-control @error('keluhan') is-invalid @enderror"
                                name="keluhan" placeholder="Masukkan keluhan Anda Minimal 10 Karakter" style="height: 100px;" required></textarea>
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
                            const khsDoneStatuses = @json($khsDoneStatuses);
                            const gpaData = @json($gpaData);

                            const typeSelect = document.getElementById('type');
                            const semesterSelect = document.getElementById('semester_id');
                            const ipkContainer = document.getElementById('ipk-container');
                            const ipkInput = document.getElementById('ipk');
                            const labelIpk = document.getElementById('label-ipk');
                            const semesterWarning = document.getElementById('semester-warning');
                            const semesterHiddenInput = document.getElementById('semester');

                            // Fungsi khusus untuk mengatur opsi Semester (Disable jika sudah KHS)
                            function updateSemesterOptions() {
                                const selectedType = typeSelect.value;

                                Array.from(semesterSelect.options).forEach(option => {
                                    if (option.value === "") return; // Skip opsi default

                                    // Disable opsi JIKA KHS dan sudah pernah diajukan
                                    if (selectedType === 'gpa_advising' && khsDoneStatuses[option.value]) {
                                        option.disabled = true;
                                        if (!option.text.includes('(Sudah Pengajuan)')) {
                                            option.text = option.text.replace(' (Selesai)', '').replace(
                                                ' (Sudah Pengajuan)', '') + ' (Sudah Pengajuan)';
                                        }
                                    } else {
                                        option.disabled = false;
                                        option.text = option.text.replace(' (Sudah Pengajuan)', '').replace(' (Selesai)',
                                            '');
                                    }
                                });
                            }

                            // Fungsi ketika 'Jenis Perwalian' diubah
                            function updateTypeBehavior() {
                                const selectedType = typeSelect.value;

                                // 1. Tampilkan IPK jika KHS
                                if (selectedType === 'gpa_advising') {
                                    ipkContainer.style.display = 'block';
                                    ipkInput.disabled.readonly = false;
                                } else {
                                    ipkContainer.style.display = 'none';
                                    ipkInput.disabled. readonly = true;
                                }

                                // 2. Perbarui status disable di dropdown semester
                                updateSemesterOptions();

                                // 3. Auto-select semester aktif jika Konseling
                                if (selectedType === 'non_gpa_advising') {
                                    // Cari option yang memiliki data-active (lebih aman menggunakan hasAttribute)
                                    const activeOption = Array.from(semesterSelect.options).find(opt => opt.hasAttribute(
                                        'data-active'));

                                    if (activeOption) {
                                        semesterSelect.value = activeOption.value;
                                    }
                                } else if (selectedType === 'gpa_advising') {
                                    // Jika kembali ke KHS dan semester yang sedang dipilih ternyata ter-disable, reset dropdown
                                    if (semesterSelect.value && semesterSelect.options[semesterSelect.selectedIndex].disabled) {
                                        semesterSelect.value = "";
                                    }
                                }

                                // 4. Update data IPK & input hidden berdasarkan semester yang terpilih saat ini
                                updateDetails();
                            }

                            // Fungsi ketika 'Semester' diubah (atau dipanggil setelah ganti Jenis Perwalian)
                            function updateDetails() {
                                const selectedType = typeSelect.value;

                                // Munculkan warning jika pilih KHS dan semesternya ternyata disable
                                if (selectedType === 'gpa_advising' && semesterSelect.value && semesterSelect.options[semesterSelect
                                        .selectedIndex].disabled) {
                                    semesterSelect.value = ""; // Force reset
                                    semesterWarning.innerText = "Anda sudah melakukan pengajuan Perwalian KHS untuk semester ini.";
                                    semesterWarning.style.display = 'block';
                                } else {
                                    semesterWarning.style.display = 'none';
                                }

                                // Ambil data nilai IPK dan update hidden input
                                if (semesterSelect.value) {
                                    if (gpaData[semesterSelect.value] !== undefined) {
                                        ipkInput.value = gpaData[semesterSelect.value];
                                    } else {
                                        ipkInput.value = '';
                                    }

                                    let semText = semesterSelect.options[semesterSelect.selectedIndex].text;
                                    // Bersihkan text dari embel-embel agar rapi masuk ke database
                                    semText = semText.replace(' (Semester Aktif)', '').replace(' (Sudah Pengajuan)', '').trim();

                                    labelIpk.innerText = 'IPK Semester ' + semText;
                                    semesterHiddenInput.value = semText;
                                } else {
                                    ipkInput.value = '';
                                    labelIpk.innerText = 'IPK Semester';
                                    semesterHiddenInput.value = '';
                                }
                            }

                            // Event listener dipisah agar tidak tabrakan
                            typeSelect.addEventListener('change', updateTypeBehavior);
                            semesterSelect.addEventListener('change', updateDetails);

                            // Jalankan sekali saat halaman pertama kali dimuat
                            updateTypeBehavior();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    </div>
    @include('_partials.alert')
@endsection
