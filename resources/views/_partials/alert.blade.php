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
                    alertTitle = 'Yakin ajukan perwalian Non KHS?';
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
</script>

{{-- ALERT ERROR
@if ($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- ALERT SUCCESS --}}
{{-- @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif --}}
