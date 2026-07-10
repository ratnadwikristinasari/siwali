@section('content')
    <div class="row gy-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100 overflow-hidden student-hero-card">
                <div class="card-body position-relative p-4 p-md-5">
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-md-8">
                            <p class="text-muted mb-1">Dashboard Orang Tua</p>
                            <h4 class="mb-2 text-wrap">SELAMAT DATANG, ORANG TUA {{ $dashboardStudentName ?? 'mahasiswa' }}.
                            </h4>
                            <p class="mb-3 text-muted">Berikut adalah ringkasan akademik dari anak Anda.</p>
                            <div class="d-flex flex-wrap align-items-end gap-3">
                                <div>
                                    <small class="text-muted d-block">Rata-rata IPK</small>
                                    <h2 class="text-primary mb-0">{{ number_format($rataIPK, 2) }}</h2>
                                </div>

                            </div>
                            <a href="{{ route('dataperwalian') }}" class="btn btn-sm btn-primary">Lihat Riwayat</a>
                        </div>
                        <div class="col-12 col-md-4 text-md-end text-center">
                            <img src="{{ asset('assets/img/illustrations/trophy.png') }}" class="student-hero-image"
                                alt="Rata-rata IPK mahasiswa">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Total Perwalian KHS</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded shadow-xs">
                                <i class="ri-book-marked-line ri-22px"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalwali }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-2">Total Konseling</p>
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded shadow-xs">
                                <i class="ri-book-marked-line ri-22px"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ $totalNonPerwalian }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5 class="mb-1">Grafik Perkembangan IPK</h5>
                    <small class="text-muted">Visualisasi IPK per semester dari tabel advise mahasiswa</small>
                </div>
                <div class="card-body pt-3">
                    <div id="weeklyOverviewChart"></div>
                </div>
            </div>
        </div>
    </div>

    @push('page-style')
        <style>
            .student-hero-image {
                width: 92px;
                max-width: 100%;
                height: auto;
            }

            @media (max-width: 767.98px) {
                .student-hero-card .card-body {
                    padding: 1.25rem;
                }

                .student-hero-image {
                    width: 76px;
                }
            }
        </style>
    @endpush

    {{-- <script>
        window.weeklyOverviewData = {
            semester: @json($semesterLabels),
            ipk: @json($valueipk)
        };
    </script> --}}
@endsection
