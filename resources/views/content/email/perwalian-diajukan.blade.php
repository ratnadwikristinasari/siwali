<h1>Hai, {{ $perwalian->lecture->name ?? '_' }}</h1>
<h3>Terdapat Pengajuan dari Mahasiswa Anda</h3>

<p>Mahasiswa telah mengajukan perwalian :</p>
<ul>
    <li>Nama: {{ $perwalian->student->name ?? '-' }}</li>
    <li>IPK: {{ $perwalian->ipk }}</li>
    <li>Keluhan: {{ $perwalian->keluhan }}</li>
</ul>
<p>
Silakan login ke Si Wali untuk melakukan verifikasi perwalian.
</p>

<a href="{{ route('login')}}"
   style="
       background-color:#0d6efd;
       color:#ffffff;
       padding:10px 18px;
       text-decoration:none;
       border-radius:6px;
       display:inline-block;
   ">
    Lihat Perwalian Mahasiswa
</a>
