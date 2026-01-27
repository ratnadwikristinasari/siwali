

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
      <div class="card-body pt-lg-10">
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
                <h5 class="mb-0">24</h5>
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
                <h5 class="mb-0">300</h5>
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
                <p class="mb-0">Belum Perwalian</p>
                <h5 class="mb-0">201</h5>
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
                <p class="mb-0">Selesai Perwalian</p>
                <h5 class="mb-0">99</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Transactions -->

  <!-- Weekly Overview Chart -->
  <div class="col-xl-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-1">10 IPK Tertinggi Mahasiswa Teknologi Informasi</h5>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="me-2">
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
                                    <option value="" selected>-- Pilih Program Studi --</option>
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

  </div>
  <!--/ Weekly Overview Chart -->

</div>


@endsection
