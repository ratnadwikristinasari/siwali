<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            html: `
                <ul style="text-align:left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `
        });
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        // Ambil form utama
        const form = document.getElementById('formPerwalian');
        const btnSubmit = document.getElementById('btn-submit');
        const selectType = document.getElementById('type');

        // Pastikan elemen-elemen ada agar tidak error di halaman lain
        if (form && btnSubmit && selectType) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah form langsung dikirim

                // Ambil nilai jenis perwalian yang dipilih
                const jenisPerwalian = selectType.value;
                let alertTitle = '';
                let alertText = '';

                // Sesuaikan teks alert berdasarkan pilihan dropdown
                if (jenisPerwalian === 'gpa_advising') {
                    alertTitle = 'Yakin ajukan perwalian?';
                    alertText = 'Pastikan data dan KHS sudah benar';
                } else if (jenisPerwalian === 'non_gpa_advising') {
                    alertTitle = 'Yakin ajukan Konseling?';
                    alertText = 'Pastikan data Anda sudah benar';
                } else {
                    Swal.fire('Perhatian', 'Silakan pilih Jenis Perwalian terlebih dahulu', 'warning');
                    return; // Hentikan proses jika belum memilih
                }

                Swal.fire({
                    title: alertTitle,
                    text: alertText,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ajukan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    backdrop: 'rgba(0,0,0,0.6)',
                    allowOutsideClick: false,
                    allowEscapeKey: false,

                }).then((result) => {
                    if (result.isConfirmed) {

                        // 🔥 POPUP LOADING
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu',
                            backdrop: 'rgba(0,0,0,0.6)',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        btnSubmit.disabled = true;

                        // 💡 OPSIONAL: Jika rute (action) untuk Non-KHS berbeda dengan KHS,
                        // kita ubah action form-nya secara dinamis di sini sebelum disubmit
                        if (jenisPerwalian === 'non_gpa_advising') {
                            form.action = "{{ route('perwalian.nonkhs') }}";
                        } else {
                            form.action = "{{ route('perwalian.store') }}";
                        }

                        // Submit form asli
                        form.submit();
                    }
                });
            });
        }
    });

    //Alert untuk tombol "Terima Perwalian" di halaman detail perwalian dosen
    document.addEventListener('DOMContentLoaded', function() {
        const formTerima = document.getElementById('perwalian.update');
        const btnSubmitTerima = document.getElementById('btn-submit-terima');
        // Kita berasumsi textarea bernama "masukan" wajib diisi
        const inputMasukan = document.querySelector('textarea[name="masukan"]');

        if (formTerima && btnSubmitTerima) {
            btnSubmitTerima.addEventListener('click', function(e) {
                e.preventDefault();

                // Validasi agar Dosen mengisi masukan terlebih dahulu
                if (inputMasukan) {
                const nilaiMasukan = inputMasukan.value.trim();
                if (nilaiMasukan === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan isi Masukan Dosen Wali terlebih dahulu!'
                    });
                    return; // Hentikan eksekusi
                } else if (nilaiMasukan.length < 10) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Masukan Dosen Wali harus minimal 10 karakter!'
                    });
                    return; // Hentikan eksekusi
                }
            }

                Swal.fire({
                    title: 'Yakin terima perwalian?',
                    text: 'Data perwalian akan disetujui dan disimpan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terima',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    backdrop: 'rgba(0,0,0,0.6)',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu',
                            backdrop: 'rgba(0,0,0,0.6)',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => { Swal.showLoading(); }
                        });

                        btnSubmitTerima.disabled = true;

                        // Form langsung disubmit ke rute yang sudah tertulis di HTML (perwalian.update)
                        formTerima.submit();
                    }
                });
            });
        }
    });
</script>

