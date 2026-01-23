        <div class="col-lg-12">
            <div class="card h-80">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Total Perwalian</h5>
                        <div class="dropdown">
                        </div>
                    </div>
                    <div class="card-body pt-lg-12">
                        <div class="row g-12">
                            <div class="col-md-3 col-2">
                                <a href="history/perwaliandosen">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-primary rounded shadow-xs">
                                                <i class="ri-group-line ri-24px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="ms-3">
                                    <p class="mb-0">Total Mahasiswa Wali</p>
                                    <h3 class="mb-0">{{ $totalperwalian['totalMahasiswa'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3 col-2">
                                <a href="history/perwaliandosen">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-warning rounded shadow-xs">
                                                <i class="ri-group-line ri-24px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="ms-3">
                                    <p class="mb-0">Total Belum Perwalian</p>
                                    <h3 class="mb-0">{{ $totalperwalian['totalBelum'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3 col-2">
                                <a href="history/perwaliandosen">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-warning rounded shadow-xs">
                                                <i class="ri-group-line ri-24px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="ms-3">
                                    <p class="mb-0">Total Perwalian Pending</p>
                                    <h3 class="mb-0">{{ $totalperwalian['totalPending'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3 col-2">
                                <a href="history/perwaliandosen">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar">
                                            <div class="avatar-initial bg-success rounded shadow-xs">
                                                <i class="ri-group-line ri-24px"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <div class="ms-3">
                                    <p class="mb-0">Total Perwalian Selesai</p>
                                    <h3 class="mb-0">{{ $totalperwalian['totalDone'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview -->
        <div class="col-xl-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-1">10 IPK Tertinggi Mahasiswa Wali</h5>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="mr-2">
                                <select id="session_id" class="form-select-sm @error('session_id') is-invalid @enderror"
                                    name="session_id">
                                    <option value="" selected>-- Pilih Tahun Ajaran --</option>
                                    @foreach ($sessions as $session)
                                        <option value="{{ $session['value'] }}">
                                            {{ $session['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <select id="semester_id"
                                    class="form-select-sm @error('semester_id') is-invalid @enderror" disabled
                                    name="semester_id">
                                    <option value="" selected>-- Pilih Semester --</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card-body pt-lg-2">
                        <!-- PODIUM -->
                        <div id="leaderboard-podium" class="row text-center mb-4"></div>

                        <!-- TABLE 4 - 10 -->
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" id="leaderboard-table">
                                <thead>
                                    <tr>
                                        <th width="60">Rank</th>
                                        <th>Nama Mahasiswa</th>
                                        <th width="80">IPK</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @push('page-style')
            <style>
                #leaderboard-podium h1 {
                    font-size: 3rem;
                }

                #leaderboard-podium .card {
                    border-radius: 12px;
                }

                #leaderboard-podium .order-0 {
                    transform: scale(1.1);
                }
            </style>
        @endpush

        @push('page-script')
            <script>
                let $session, $semester;

                document.addEventListener('DOMContentLoaded', function() {
                    $session = $('#session_id');
                    $semester = $('#semester_id');

                    // ketika tahun ajaran berubah
                    $session.on('change', function() {
                        const sessionId = $(this).val();
                        resetSemester();
                        console.log("asd")

                        if (!sessionId) return;

                        fetchSemesters(sessionId).done(function(res) {
                            populateSelect(
                                $semester,
                                res.data,
                                '-- Pilih Semester --'
                            );
                            setDisabled($semester, false);
                        });
                    });

                    // ketika semester berubah → load leaderboard
                    $semester.on('change', function() {
                        const semesterId = $(this).val();
                        if (!semesterId) return;

                        loadLeaderboard(semesterId);
                    });

                    function loadLeaderboard(semesterId) {
                        $.ajax({
                            url: "{{ route('dashboard.top-ipk') }}",
                            method: 'GET',
                            data: {
                                semester_id: semesterId
                            },
                            success: function(res) {
                                renderLeaderboard(res);
                            },
                            error: function() {
                                alert('Gagal memuat data leaderboard');
                            }
                        });
                    }

                    function setDisabled($el, disabled) {
                        $el.prop('disabled', !!disabled);
                    }

                    function resetSemester() {
                        setDisabled($semester, true);
                        $semester.html('<option value="">-- Pilih Semester --</option>');
                    }

                    function populateSelect($select, items, placeholder) {
                        const opts = [`<option value="">${placeholder}</option>`]
                            .concat(items.map(it =>
                                `<option value="${it.value}">${it.label}</option>`
                            ));
                        $select.html(opts.join(''));
                    }

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

                    function renderLeaderboard(data) {
                        const names = data.categories || [];
                        const ipks = data.series || [];

                        const podium = $('#leaderboard-podium');
                        const tbody = $('#leaderboard-table tbody');

                        podium.empty();
                        tbody.empty();

                        if (!names.length) {
                            podium.html('<p class="text-muted">Data tidak tersedia</p>');
                            return;
                        }

                        const podiumOrder = [{
                                idx: 1,
                                label: '🥈'
                            },
                            {
                                idx: 0,
                                label: '🥇'
                            },
                            {
                                idx: 2,
                                label: '🥉'
                            },
                        ];

                        podiumOrder.forEach(p => {
                            if (!names[p.idx]) return;

                            podium.append(`
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h1>${p.label}</h1>
                            <h6>${names[p.idx]}</h6>
                            <span class="badge bg-primary">IPK ${ipks[p.idx]}</span>
                        </div>
                    </div>
                </div>
            `);
                        });

                        for (let i = 3; i < names.length; i++) {
                            tbody.append(`
                <tr>
                    <td>${i + 1}</td>
                    <td>${names[i]}</td>
                    <td><strong>${ipks[i]}</strong></td>
                </tr>
            `);
                        }
                    }
                });
            </script>
        @endpush
