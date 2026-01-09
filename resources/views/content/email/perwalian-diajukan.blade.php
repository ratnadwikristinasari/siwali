<h3>Pengajuan Perwalian</h3>

<p>Mahasiswa telah mengajukan perwalian.</p>

<ul>
    <li>Nama: {{ $perwalian->student->name ?? '-' }}</li>
    <li>IPK: {{ $perwalian->ipk }}</li>
    <li>Keluhan: {{ $perwalian->keluhan }}</li>
</ul>
<p>
Silakan login ke Si Wali untuk melakukan verifikasi perwalian.
</p>
