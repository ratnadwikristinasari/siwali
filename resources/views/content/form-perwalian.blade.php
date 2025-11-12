@extends('layouts/contentNavbarLayout')

@section('title', ' Vertical Layouts - Forms')

@section('content')
<!-- Basic Layout -->
<div class="row">
  <div class="col-xl">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Silakan Upload Kartu Hasil Studi</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="form-floating form-floating-outline mb-6">
            <input type="text" class="form-control" id="basic-default-fullname" placeholder="John Doe" />
            <label for="basic-default-fullname">Full Name</label>
          </div>
          <div class="form-floating form-floating-outline mb-6">
            <input type="text" class="form-control" id="basic-default-company" placeholder="ACME Inc." />
            <label for="basic-default-company">Company</label>
          </div>
          <div class="mb-6">
            <div class="input-group input-group-merge">
              <div class="form-floating form-floating-outline">
                <input type="text" id="basic-default-email" class="form-control" placeholder="john.doe" aria-label="john.doe" aria-describedby="basic-default-email2" />
                <label for="basic-default-email">Email</label>
              </div>
              <span class="input-group-text" id="basic-default-email2">@example.com</span>
            </div>
            <div class="form-text"> You can use letters, numbers & periods </div>
          </div>
          <div class="form-floating form-floating-outline mb-6">
            <input type="text" id="basic-default-phone" class="form-control phone-mask" placeholder="658 799 8941" />
            <label for="basic-default-phone">Phone No</label>
          </div>
          <div class="form-floating form-floating-outline mb-6">
            <textarea id="basic-default-message" class="form-control" placeholder="Hi, Do you have a moment to talk Joe?" style="height: 60px;"></textarea>
            <label for="basic-default-message">Message</label>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
<!-- Merged -->
  <div class="col-xl">
    <div class="card mb-6">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Perwalian</h5>
      </div>
      <div class="card-body">
        <form>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-fullname2" class="input-group-text"><i class="ri-user-line ri-20px"></i></span>
              <input type="text" class="form-control" id="basic-icon-default-fullname" placeholder="Nama Lengkap" aria-label="namalengkap" aria-describedby="basic-icon-default-fullname2" />
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-company2" class="input-group-text"><i class="ri-building-4-line ri-20px"></i></span>
              <input type="text" id="basic-icon-default-company" class="form-control" placeholder="Semester" aria-label="semester" aria-describedby="basic-icon-default-company2" />
          </div>
          <div class="mb-6">
            <div class="input-group input-group-merge">
              <span class="input-group-text"><i class="ri-mail-line ri-20px"></i></span>
                <input type="text" id="basic-icon-default-email" class="form-control" placeholder="Program Studi" aria-label="john.doe" aria-describedby="basic-icon-default-email2" />
              <span id="basic-icon-default-email2" class="input-group-text">@example.com</span>
            </div>
            <div class="form-text"> You can use letters, numbers & periods </div>
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-phone2" class="input-group-text"><i class="ri-phone-fill ri-20px"></i></span>
              <input type="text" id="basic-icon-default-phone" class="form-control phone-mask" placeholder="IPK" aria-label="658 799 8941" aria-describedby="basic-icon-default-phone2" />
          </div>
          <div class="input-group input-group-merge mb-6">
            <span id="basic-icon-default-message2" class="input-group-text"><i class="ri-chat-4-line ri-20px"></i></span>
              <textarea id="basic-icon-default-message" class="form-control" placeholder="" aria-label="Hi, Do you have a moment to talk Joe?" aria-describedby="basic-icon-default-message2" style="height: 60px;"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
