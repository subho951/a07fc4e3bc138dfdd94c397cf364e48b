<div class="container">
  <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
          <div class="d-flex justify-content-center py-4">
            <a href="<?=url('admin')?>" class="d-flex align-items-center w-auto">
              <img src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>">
              <!-- <span class="d-none d-lg-block"><?=$generalSetting->site_name?></span> -->
            </a>
          </div><!-- End Logo -->
          <div class="card mb-3">
            <div class="card-body">
              <div class="pt-4 pb-2">
                <h5 class="card-title text-center pb-0 fs-4">Reset Your Account</h5>
              </div>
              @if(session('success_message'))
                <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show autohide" role="alert">
                  {{ session('success_message') }}
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif
              @if(session('error_message'))
                <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show autohide" role="alert">
                  {{ session('error_message') }}
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              @endif
              <form method="POST" action="" class="row g-3">
                @csrf
                <div class="col-12">
                  <label for="new_password" class="form-label">New Password</label>
                  <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend"><i class="fas fa-key"></i></span>
                    <input type="password" name="new_password" class="form-control" id="new_password" required>
                  </div>
                </div>
                <div class="col-12">
                  <label for="old_password" class="form-label">Confirm Password</label>
                  <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend"><i class="fas fa-key"></i></span>
                    <input type="password" name="old_password" class="form-control" id="old_password" required>
                  </div>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Submit</button>
                </div>
              </form>
            </div>
          </div>
          <div class="credits">
            Designed & Developed by <a target="_blank" href="https://subhomoysamanta.info/">Subhomoy Samanta</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>