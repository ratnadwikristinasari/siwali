@php
    $data = $userData['data'];
    $student = $data['student_detail'];
    $activeSemester = collect($student['student_semester'] ?? [])->firstWhere('is_active', true);
    $supervisor = $student['supervisor_lectures'][0] ?? null;
    $statusColor = $data['status'] === 'ACTIVE' ? 'success' : 'secondary';
    $birthDate = $data['birth_date'] ? \Carbon\Carbon::parse($data['birth_date'])->translatedFormat('d F Y') : '-';
@endphp

<div class="row g-4 mb-4">
    {{-- Profile Card --}}
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body text-center pt-5 pb-4">
                <div class="avatar avatar-xl mx-auto mb-0">
                    <div class="avatar-initial bg-label-primary rounded-circle" style="font-size: 2rem;">
                        {{ strtoupper(substr($data['name'], 0, 2)) }}
                    </div>
                </div>
                <h5 class="mb-1">{{ $data['name'] }}</h5>
                <p class="text-muted mb-0">{{ $student['nim'] ?? '-' }}</p>
                <span class="badge bg-label-{{ $statusColor }} rounded-pill px-3 py-1 mb-3">
                    {{ $data['status'] }}
                </span>

                <div class="d-flex justify-content-center gap-4 mt-0">
                    <div class="text-center">
                        <h6 class="mb-0">{{ $student['generation'] ?? '-' }}</h6>
                        <small class="text-muted">Angkatan</small>
                    </div>
                    <div class="vr"></div>
                    <div class="text-center">
                        <h6 class="mb-0">{{ $activeSemester['semester'] ?? '-' }}</h6>
                        <small class="text-muted">Semester</small>
                    </div>
                    <div class="vr"></div>
                    <div class="text-center">
                        <h6 class="mb-0">{{ $activeSemester['class'] ?? '-' }}</h6>
                        <small class="text-muted">Kelas</small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center gap-2 py-0">
                            <i class="bx bx-envelope text-primary"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-medium small"></span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="bx bx-phone text-primary"></i>
                            <div>
                                <span class="fw-medium small">{{ $data['email'] ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                            <i class="bx bx-phone text-primary"></i>
                            <div>
                                <small class="text-muted d-block">No. Telepon</small>
                                <span class="fw-medium small">{{ $data['phone_number'] ?? '-' }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center gap-2 py-2">
                            <i class="bx bx-map text-primary"></i>
                            {{-- <div>
                                <small class="text-muted d-block">Alamat</small>
                                <span class="fw-medium small">{{ $data['address'] ?? '-' }}</span>
                            </div> --}}
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-footer bg-transparent">

            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="col-xl-8 col-md-7">
        <div class="row g-4">
            {{-- Informasi Akademik --}}
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-book-open me-2 text-primary"></i>INFORMASI AKADEMIK
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Program
                                    Studi</small>
                                <p class="mb-0 fw-medium">{{ $student['study_program_name'] ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold"
                                    style="font-size: 0.7rem;">Jurusan</small>
                                <p class="mb-0 fw-medium">{{ $student['major_name'] ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Dosen
                                    Wali</small>
                                <p class="mb-0 fw-medium">{{ $supervisor['name'] ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Tahun
                                    Akademik</small>
                                <p class="mb-0 fw-medium">{{ $activeSemester['session'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Pribadi --}}
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-user me-2 text-primary"></i>INFORMASI PRIBADI
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Tempat,
                                    Tanggal Lahir</small>
                                <p class="mb-0 fw-medium">{{ $data['birth_place'] ?? '-' }}, {{ $birthDate }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Jenis
                                    Kelamin</small>
                                <p class="mb-0 fw-medium">
                                    @if ($data['gender'] === 'MALE')
                                        <i class="bx bx-male text-info me-1"></i>Laki-laki
                                    @elseif($data['gender'] === 'FEMALE')
                                        <i class="bx bx-female text-danger me-1"></i>Perempuan
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold"
                                    style="font-size: 0.7rem;">Agama</small>
                                <p class="mb-0 fw-medium">{{ ucfirst(strtolower($data['religion'] ?? '-')) }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold"
                                    style="font-size: 0.7rem;">Kewarganegaraan</small>
                                <p class="mb-0 fw-medium">{{ ucfirst(strtolower($data['nationality'] ?? '-')) }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-semibold"
                                    style="font-size: 0.7rem;">Alamat</small>
                                <p class="mb-0 fw-medium">{{ $data['address'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <h5 class="mb-1">Grafik Perkembangan IPK</h5>
                <small class="text-muted">Visualisasi IPK per semester</small>
            </div>
            <div class="card-body pt-3">
                <div id="weeklyOverviewChart"></div>
            </div>
        </div>
    </div>
    {{-- Riwayat Semester --}}
    <div class="col-12">
        <div class="card overflow-hidden">
            <div class="row g-2 align-items-center my-3 mx-1">
                <div class="col-12 col-md">
                    <h6 class="mb-0">
                        <i class="bx bx-calendar me-2 text-primary"></i>Riwayat Semester
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Semester</th>
                                    <th>Tahun Akademik</th>
                                    <th>Kelas</th>
                                    <th>Status Akademik</th>
                                    <th>Status Perwalian</th>
                                    <th>KHS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($student['student_semester'] ?? [] as $sem)
                                    <tr>
                                        <td>Semester {{ $sem['semester'] }}</td>
                                        <td>{{ $sem['session'] }}</td>
                                        <td>{{ $sem['class'] }}</td>
                                        <td>
                                            @if ($sem['is_active'])
                                                <span class="badge bg-label-success rounded-pill">Aktif</span>
                                            @else
                                                <span class="badge bg-label-secondary rounded-pill">
                                                    Naik</span>
                                            @endif
                                        </td>
                                        {{-- <td>
                                            @if ($sem['status_perwalian'] === null)
                                                <span class="badge bg-label-secondary rounded-pill">
                                                    Belum Perwalian
                                                </span>
                                            @elseif ($sem['status_perwalian'] === 'pending')
                                                <span class="badge bg-label-warning rounded-pill">
                                                    Menunggu Tanda Tangan Dosen Wali
                                                </span>
                                            @elseif ($sem['status_perwalian'] === 'signed')
                                                <span class="badge bg-label-info rounded-pill">
                                                    Menunggu Tanda Tangan Kajur
                                                </span>
                                            @elseif ($sem['status_perwalian'] === 'done')
                                                <span class="badge bg-label-success rounded-pill">
                                                    Selesai
                                                </span>
                                            @endif
                                        </td> --}}
                                        <td>
                                            <a class="btn btn-icon btn-sm btn-outline-primary"
                                                href="{{ route('datamahasiswa.preview-gpa', ['studentId' => $id, 'semesterId' => $sem['id']]) }}"
                                                title="Lihat KHS" target="_blank" rel="noopener">
                                                <i class="ri-file-list-3-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data
                                            semester</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>
    </div>
</div>
