@section('content')
    <div class="row gy-6">
        <!-- Transactions -->
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Data Jurusan Teknologi Informasi</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-6">
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-primary rounded shadow-xs">
                                        <i class="ri-pie-chart-2-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Dosen</p>
                                    <h5 class="mb-0">{{ $analyticsSummary['total_lecturers'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-success rounded shadow-xs">
                                        <i class="ri-group-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Mahasiswa</p>
                                    <h5 class="mb-0">{{ $analyticsSummary['total_students'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-warning rounded shadow-xs">
                                        <i class="ri-macbook-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Program Studi</p>
                                    <h5 class="mb-0">{{ $analyticsSummary['total_study_programs'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                    <div class="avatar-initial bg-info rounded shadow-xs">
                                        <i class="ri-money-dollar-circle-line ri-24px"></i>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <p class="mb-0">Jurusan</p>
                                    <h5 class="mb-0">{{ $analyticsSummary['total_majors'] ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Transactions -->

        {{-- Top IPK Leaderboard --}}
        <div class="col-xl-12 col-md-12">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h5 class="card-title mb-1">10 IPK Tertinggi Mahasiswa
                            </h5>
                            <small class="text-muted" id="session-info">Pilih tahun ajaran untuk melihat data</small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <select id="session_id" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @forelse($sessions as $session)
                                    <option value="{{ $session['value'] }}">{{ $session['label'] }}</option>
                                @empty
                                @endforelse
                            </select>
                            <select id="semester_id" class="form-select form-select-sm" style="width: 180px;" disabled>
                                <option value="">-- Semua Semester --</option>
                            </select>
                            <select id="prodi_id" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">-- Semua Program Studi --</option>
                                @forelse($prodiList as $prodi)
                                    <option value="{{ $prodi['value'] }}">{{ $prodi['label'] }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- PODIUM 1-3 -->
                    <div id="leaderboard-podium" class="row text-center mb-4 g-3">
                        <div class="col-12 text-muted">Pilih filter dulu untuk melihat leaderboard</div>
                    </div>

                    <!-- TABLE 4-10 -->
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">Rank</th>
                                    <th>Nama Mahasiswa</th>
                                    <th width="80" class="text-end">IPK</th>
                                </tr>
                            </thead>
                            <tbody id="leaderboard-table">Pilih filter dulu untuk melihat data
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @push('page-style')
            <style>
                #leaderboard-podium h1 {
                    font-size: 2.5rem;
                    margin: 0;
                }

                #leaderboard-podium .card {
                    border-radius: 12px;
                    border: 0;
                    background: linear-gradient(135deg, #f5f7fa 0%, #fff 100%);
                    transition: transform 0.3s;
                }

                #leaderboard-podium .card:hover {
                    transform: translateY(-4px);
                }

                #leaderboard-podium .badge {
                    font-size: 0.85rem;
                    padding: 0.4rem 0.8rem;
                }

                #leaderboard-podium h6 {
                    font-weight: 600;
                    font-size: 0.95rem;
                    margin-bottom: 0.5rem;
                }

                .table-hover tbody tr:hover {
                    background-color: rgba(0, 123, 255, 0.05);
                }

                .table tbody td {
                    vertical-align: middle;
                    padding: 1rem 0.75rem;
                }

                .rank-badge {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    font-weight: 600;
                    font-size: 0.9rem;
                }

                .rank-4-10 {
                    background-color: #f0f0f0;
                    color: #666;
                }
            </style>
        @endpush

        @push('page-script')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    $session = $('#session_id');
                    $semester = $('#semester_id');
                    $prodi = $('#prodi_id');

                    // When session changes, fetch semester options
                    $session.on('change', function() {
                        const sessionId = $(this).val();
                        const sessionText = $session.find('option:selected').text();

                        // Update session info text
                        if (sessionId) {
                            $('#session-info').text('Tahun Ajaran: ' + sessionText);
                        } else {
                            $('#session-info').text('Pilih tahun ajaran untuk melihat data');
                        }

                        resetSemester();

                        if (!sessionId) return;

                        fetchSemesters(sessionId).done(function(res) {
                            populateSemesterSelect(res.data || []);
                            $semester.prop('disabled', false);
                        }).fail(function() {
                            $semester.prop('disabled', true);
                        });
                    });

                    // When semester changes
                    $semester.on('change', function() {
                        const semesterId = $(this).val();
                        loadLeaderboard(semesterId);
                    });

                    // When prodi changes
                    $prodi.on('change', function() {
                        const semesterId = $semester.val();
                        loadLeaderboard(semesterId);
                    });

                    function fetchSemesters(sessionId) {
                        return $.ajax({
                            url: "{{ route('api.semester.option') }}",
                            method: 'GET',
                            dataType: 'json',
                            data: {
                                session_id: sessionId
                            }
                        });
                    }

                    function resetSemester() {
                        $semester.prop('disabled', true);
                        $semester.html('<option value="">-- Semua Semester --</option>');
                    }

                    function populateSemesterSelect(semesters) {
                        let opts = '<option value="">-- Semua Semester --</option>';
                        semesters.forEach(item => {
                            opts += `<option value="${item.value}">Semester ${item.label}</option>`;
                        });
                        $semester.html(opts);
                    }

                    function loadLeaderboard(semesterId) {
                        $.ajax({
                            url: "{{ route('dashboard.top-ipk') }}",
                            method: 'GET',
                            data: {
                                prodi_id: $prodi.val(),
                                semester_id: semesterId
                            },
                            success: function(res) {
                                renderLeaderboard(res);
                            },
                            error: function() {
                                $('#leaderboard-podium').html(
                                    '<div class="col-12 text-danger">Gagal memuat data</div>');
                                $('#leaderboard-table').html(
                                    '<tr><td colspan="3" class="text-center text-danger">Error</td></tr>');
                            }
                        });
                    }

                    function renderLeaderboard(data) {
                        const names = data.categories || [];
                        const ipks = data.series || [];

                        const podium = $('#leaderboard-podium');
                        const tbody = $('#leaderboard-table');

                        podium.empty();
                        tbody.empty();

                        if (!names.length) {
                            podium.html('<div class="col-12 text-muted">Data tidak tersedia</div>');
                            tbody.html(
                                '<tr><td colspan="3" class="text-center text-muted py-4">Data tidak tersedia</td></tr>');
                            return;
                        }

                        // Podium medals
                        const podiumMedals = [{
                                idx: 0,
                                label: '🥇',
                                bg: 'bg-warning'
                            },
                            {
                                idx: 1,
                                label: '🥈',
                                bg: 'bg-secondary'
                            },
                            {
                                idx: 2,
                                label: '🥉',
                                bg: 'bg-info'
                            }
                        ];

                        podiumMedals.forEach(m => {
                            if (!names[m.idx]) return;

                            podium.append(`
              <div class="col-md-4">
                <div class="card shadow-sm">
                  <div class="card-body py-4">
                    <h1>${m.label}</h1>
                    <h6 class="text-truncate">${names[m.idx]}</h6>
                    <span class="badge bg-primary">IPK ${parseFloat(ipks[m.idx]).toFixed(2)}</span>
                  </div>
                </div>
              </div>
            `);
                        });

                        // Table rows 4-10
                        for (let i = 3; i < names.length; i++) {
                            tbody.append(`
              <tr>
                <td>
                  <span class="rank-badge rank-4-10">${i + 1}</span>
                </td>
                <td>${names[i]}</td>
                <td class="text-end">
                  <strong class="text-primary">${parseFloat(ipks[i]).toFixed(2)}</strong>
                </td>
              </tr>
            `);
                        }
                    }
                });
            </script>
        @endpush

    </div>
    <!--/ Top IPK -->

    </div>
@endsection
