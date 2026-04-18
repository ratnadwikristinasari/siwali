<div class="row gy-6">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="card-title mb-1">Dashboard Perwalian Dosen</h5>
                        <small class="text-muted" id="session-info">Pilih tahun ajaran untuk melihat data</small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <select id="session_id" class="form-select form-select-sm" style="width: 220px;">
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session['value'] }}">{{ $session['label'] }}</option>
                            @endforeach
                        </select>
                        <select id="semester_id" class="form-select form-select-sm" style="width: 180px;" disabled>
                            <option value="">-- Pilih Semester --</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4" id="analytics-cards">
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded bg-label-primary h-100">
                            <p class="mb-1 text-body">Total Mahasiswa Wali</p>
                            <h4 class="mb-0" id="total-mahasiswa">{{ $totalperwalian['totalMahasiswa'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded bg-label-warning h-100">
                            <p class="mb-1 text-body">Belum Perwalian</p>
                            <h4 class="mb-0" id="total-belum">{{ $totalperwalian['totalBelum'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded bg-label-info h-100">
                            <p class="mb-1 text-body">Perwalian Pending</p>
                            <h4 class="mb-0" id="total-pending">{{ $totalperwalian['totalPending'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded bg-label-success h-100">
                            <p class="mb-1 text-body">Perwalian Selesai</p>
                            <h4 class="mb-0" id="total-done">{{ $totalperwalian['totalDone'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <h5 class="mb-1">10 IPK Tertinggi Mahasiswa Wali</h5>
                <small class="text-muted">Data mengikuti semester yang dipilih pada filter di atas</small>
            </div>
            <div class="card-body">
                <div id="leaderboard-podium" class="row text-center mb-4 g-3">
                    <div class="col-12 text-muted">Pilih filter dulu untuk melihat leaderboard</div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="50">Rank</th>
                                <th>Nama Mahasiswa</th>
                                <th width="80" class="text-end">IPK</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-table">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Pilih filter dulu untuk melihat
                                    data</td>
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
            let $session, $semester;

            document.addEventListener('DOMContentLoaded', function() {
                $session = $('#session_id');
                $semester = $('#semester_id');

                // Tahun ajaran berubah -> reset semester, update judul, dan ambil semester option.
                $session.on('change', function() {
                    const sessionId = $(this).val();
                    const sessionText = $session.find('option:selected').text();

                    if (sessionId) {
                        $('#session-info').text('Tahun Ajaran: ' + sessionText);
                    } else {
                        $('#session-info').text('Pilih tahun ajaran untuk melihat data');
                    }

                    resetSemester();
                    resetLeaderboard();
                    resetAnalytics();

                    if (!sessionId) return;

                    fetchSemesters(sessionId).done(function(res) {
                        populateSemesterSelect(res.data || []);
                        $semester.prop('disabled', false);
                    }).fail(function() {
                        $semester.prop('disabled', true);
                    });
                });

                // Semester berubah -> update analytics dan leaderboard agar konsisten.
                $semester.on('change', function() {
                    const semesterId = $(this).val();

                    if (!semesterId) {
                        resetLeaderboard();
                        resetAnalytics();
                        return;
                    }

                    loadDashboardData(semesterId);
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
                    $semester.html('<option value="">-- Pilih Semester --</option>');
                }

                function populateSemesterSelect(semesters) {
                    let opts = '<option value="">-- Pilih Semester --</option>';
                    semesters.forEach(item => {
                        opts += `<option value="${item.value}">${item.label}</option>`;
                    });
                    $semester.html(opts);
                }

                function loadDashboardData(semesterId) {
                    $.when(
                        $.ajax({
                            url: "{{ route('dashboard.lecturer-analytics') }}",
                            method: 'GET',
                            dataType: 'json',
                            data: {
                                semester_id: semesterId
                            }
                        }),
                        $.ajax({
                            url: "{{ route('dashboard.top-ipk') }}",
                            method: 'GET',
                            dataType: 'json',
                            data: {
                                semester_id: semesterId
                            }
                        })
                    ).done(function(analyticsRes, leaderboardRes) {
                        // jQuery.when returns [data, status, xhr]
                        renderAnalytics(analyticsRes[0] || {});
                        renderLeaderboard(leaderboardRes[0] || {});
                    }).fail(function() {
                        resetAnalytics();
                        $('#leaderboard-podium').html(
                        '<div class="col-12 text-danger">Gagal memuat data</div>');
                        $('#leaderboard-table').html(
                            '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data</td></tr>'
                        );
                    });
                }

                function renderAnalytics(data) {
                    $('#total-mahasiswa').text(data.totalMahasiswa ?? 0);
                    $('#total-belum').text(data.totalBelum ?? 0);
                    $('#total-pending').text(data.totalPending ?? 0);
                    $('#total-done').text(data.totalDone ?? 0);
                }

                function resetAnalytics() {
                    $('#total-mahasiswa').text(0);
                    $('#total-belum').text(0);
                    $('#total-pending').text(0);
                    $('#total-done').text(0);
                }

                function resetLeaderboard() {
                    $('#leaderboard-podium').html(
                        '<div class="col-12 text-muted">Pilih filter dulu untuk melihat leaderboard</div>'
                    );
                    $('#leaderboard-table').html(
                        '<tr><td colspan="3" class="text-center text-muted py-4">Pilih filter dulu untuk melihat data</td></tr>'
                    );
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
                            '<tr><td colspan="3" class="text-center text-muted py-4">Data tidak tersedia</td></tr>'
                        );
                        return;
                    }

                    const podiumMedals = [{
                            idx: 0,
                            label: '🥇'
                        },
                        {
                            idx: 1,
                            label: '🥈'
                        },
                        {
                            idx: 2,
                            label: '🥉'
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

                    for (let i = 3; i < names.length; i++) {
                        tbody.append(`
              <tr>
                <td><span class="rank-badge rank-4-10">${i + 1}</span></td>
                <td>${names[i]}</td>
                <td class="text-end"><strong class="text-primary">${parseFloat(ipks[i]).toFixed(2)}</strong></td>
              </tr>
            `);
                    }
                }
            });
        </script>
    @endpush
</div>
