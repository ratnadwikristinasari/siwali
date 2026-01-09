<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    document.addEventListener('DOMContentLoaded', function () {
    //ambil form perwalian 
    const form = document.querySelector('form[action="{{ route('perwalian.store') }}"]');
    //ambil button submit
    const btnSubmit = document.getElementById('btn-submit');

    form.addEventListener('submit', function (e) {
        e.preventDefault(); //mencegah form langsung kirim server
        Swal.fire({
            title: 'Yakin ajukan perwalian?',
            text: 'Pastikan data dan KHS sudah benar',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {

                // 🔥 POPUP LOADING
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Disable button biar tidak double submit
                btnSubmit.disabled = true;

                // submit form asli
                form.submit();
            }
        });
    });
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
