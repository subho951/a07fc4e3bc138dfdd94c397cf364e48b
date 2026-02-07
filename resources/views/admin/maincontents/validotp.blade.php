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
                <h5 class="card-title text-center pb-0 fs-4">Please enter your OTP</h5>
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
              <form id="otpForm" method="POST" action="" class="row g-3">
                @csrf
                <div class="col-12">
                  <div class="input-group has-validation">
                    <input type="text" style="margin: 10px;height: 60px;border: 2px solid black;" name="otp1" class="form-control otpInput" id="otp1" maxlength="1" required>
                    <input type="text" style="margin: 10px;height: 60px;border: 2px solid black;" name="otp2" class="form-control otpInput" id="otp2" maxlength="1" required>
                    <input type="text" style="margin: 10px;height: 60px;border: 2px solid black;" name="otp3" class="form-control otpInput" id="otp3" maxlength="1" required>
                    <input type="text" style="margin: 10px;height: 60px;border: 2px solid black;" name="otp4" class="form-control otpInput" id="otp4" maxlength="1" required>
                    <div class="invalid-feedback">Please enter your otp.</div>
                  </div>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Submit</button>
                </div>
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
<script>
  const otpInputs = document.querySelectorAll(".otpInput");
  otpInputs.forEach((input, index) => {
      input.addEventListener("input", function() {
          if (this.value.length >= 1) {
              if (index < otpInputs.length - 1) {
                  otpInputs[index + 1].focus();
              }
          }
      });
      input.addEventListener("keydown", function(event) {
          if (event.key === "Backspace" && this.value.length === 0) {
              if (index > 0) {
                  otpInputs[index - 1].focus();
              }
          }
      });
  });
</script>