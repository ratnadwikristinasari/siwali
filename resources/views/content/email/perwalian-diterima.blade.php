<h1>Hai, {{ $perwalian->student->name ?? '_' }}</h1>
<h3>Pengajuan perwalian Anda selesai</h3>
<p>
Silakan login ke Si Wali untuk melihat hasil perwalian Anda.
</p>


<a href="{{ route('login') }}"
   style="
       background-color:#0d6efd;
       color:#ffffff;
       padding:10px 18px;
       text-decoration:none;
       border-radius:6px;
       display:inline-block;
   ">
    Lihat Hasil Perwalian
</a>