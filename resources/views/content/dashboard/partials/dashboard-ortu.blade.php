

@section('content')
<div class="row gy-6">
  <!-- Congratulations card -->
  <div class="col-md-12 col-lg-7">
    <div class="card">
      <div class="card-body text-nowrap">
        <h5 class="card-title mb-0 flex-wrap text-nowrap">Congratulations Nama Mahasiswa</h5>
        <p class="mb-2">Rata - Rata IPK</p>
        <h4 class="text-primary mb-0">3.85</h4>
        <p class="mb-2">78% of target 🚀</p>
        <a href="javascript:;" class="btn btn-sm btn-primary">Detail</a>
      </div>
      <img src="{{asset('assets/img/illustrations/trophy.png')}}" class="position-absolute bottom-0 end-0 me-8 mb-8" width="83" alt="view sales">
    </div>
  </div>
  <!--/ Congratulations card -->

  <!-- Total Perwalian -->
  <div class="col-lg-5">
    <div class="card h-74">
      <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Total Perwalian</h5>
          <div class="dropdown">
          </div>
        </div>
      <div class="card-body pt-lg-10">
        <div class="row g-6">
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-primary rounded shadow-xs">
                  <i class="ri-pie-chart-2-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-4">
                <p class="mb-0">Kartu Hasil Study</p>
                <h5 class="mb-0">2</h5>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="avatar">
                <div class="avatar-initial bg-success rounded shadow-xs">
                  <i class="ri-group-line ri-24px"></i>
                </div>
              </div>
              <div class="ms-4">
                <p class="mb-0">Non Kartu Hasil Study</p>
                <h5 class="mb-0">5</h5>
              </div>
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
  <!--/ Transactions -->

  <!-- Overview -->
  <div class="col-xl-12 col-md-6">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between">
          <h5 class="mb-1">Grafik Pencapaian</h5>
          <div class="dropdown">
            <button class="btn text-muted p-0" type="button" id="weeklyOverviewDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="ri-more-2-line ri-24px"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="weeklyOverviewDropdown">
              <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
              <a class="dropdown-item" href="javascript:void(0);">Update</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body pt-lg-2">
        <div id="weeklyOverviewChart"></div>
        <div class="mt-1 mt-md-3">
          <div class="d-flex align-items-center gap-4">
          </div>
          <div class="d-grid mt-3 mt-md-4">
          </div>
        </div>
      </div>
    </div>
    <br>
    <h4>Biodata Mahasiswa</h4>
    <div class="col-12">
    <div class="card overflow-hidden">
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th class="text-truncate">User</th>
              <th class="text-truncate">Email</th>
              <th class="text-truncate">Role</th>
              <th class="text-truncate">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Jordan Stevenson</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">susanna.Lind57@gmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-vip-crown-line ri-22px text-primary me-2"></i>
                  <span>Admin</span>
                </div>
              </td>
              <td><span class="badge bg-label-warning rounded-pill">Pending</span></td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/3.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Benedetto Rossiter</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">estelle.Bailey10@gmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-edit-box-line text-warning ri-22px me-2"></i>
                  <span>Editor</span>
                </div>
              </td>
              <td><span class="badge bg-label-success rounded-pill">Active</span></td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/2.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Bentlee Emblin</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">milo86@hotmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-computer-line text-danger ri-22px me-2"></i>
                  <span>Author</span>
                </div>
              </td>
              <td><span class="badge bg-label-success rounded-pill">Active</span></td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/5.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Bertha Biner</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">le35@hotmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-edit-box-line text-warning ri-22px me-2"></i>
                  <span>Editor</span>
                </div>
              </td>
              <td><span class="badge bg-label-warning rounded-pill">Pending</span></td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/4.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Beverlie Krabbe</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">ahmad_Collins@yahoo.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-pie-chart-2-line ri-22px text-info me-2"></i>
                  <span>Maintainer</span>
                </div>
              </td>
              <td><span class="badge bg-label-success rounded-pill">Active</span></td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/7.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Bradan Rosebotham</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">tillman.Gleason68@hotmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-edit-box-line text-warning ri-22px me-2"></i>
                  <span>Editor</span>
                </div>
              </td>
              <td><span class="badge bg-label-warning rounded-pill">Pending</span></td>
            </tr>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/6.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Bree Kilday</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">otho21@gmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-user-3-line ri-22px text-success me-2"></i>
                  <span>Subscriber</span>
                </div>
              </td>
              <td><span class="badge bg-label-success rounded-pill">Active</span></td>
            </tr>
            <tr class="border-transparent">
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm me-4">
                    <img src="{{asset('assets/img/avatars/1.png')}}" alt="Avatar" class="rounded-circle">
                  </div>
                  <div>
                    <h6 class="mb-0 text-truncate">Breena Gallemore</h6>
                  </div>
                </div>
              </td>
              <td class="text-truncate">florencio.Little@hotmail.com</td>
              <td class="text-truncate">
                <div class="d-flex align-items-center">
                  <i class="ri-user-3-line ri-22px text-success me-2"></i>
                  <span>Subscriber</span> 
                </div>
              </td>
              <td><span class="badge bg-label-secondary rounded-pill">Inactive</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>
@endsection
