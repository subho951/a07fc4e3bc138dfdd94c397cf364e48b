<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><a href="<?=url('admin/' . $controllerRoute . '/list/')?>"><?=$module['title']?> List</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section profile">
  <div class="row">
    <div class="col-xl-12">
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
    </div>
    <?php
    if($row){
      $name               = $row->name;
      $email              = $row->email;
      $phone              = $row->phone;
      $photo              = $row->photo;
      $designation        = $row->designation;
      $dob                = $row->dob;
      $biodata            = $row->biodata;
    } else {
      $name               = '';
      $email              = '';
      $phone              = '';
      $photo              = '';
      $designation        = '';
      $dob                = '';
      $biodata            = '';
    }
    ?>
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body pt-3">
          <h6 class="text-danger">Star (*) marks fields are mandatory</h6>
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
              <label for="name" class="col-md-2 col-lg-2 col-form-label">Name <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="name" class="form-control" id="name" value="<?=$name?>" required>
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="email" class="col-md-2 col-lg-2 col-form-label">Email <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="email" name="email" class="form-control" id="email" value="<?=$email?>" required>
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="phone" class="col-md-2 col-lg-2 col-form-label">Phone <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="phone" class="form-control" id="phone" value="<?=$phone?>" maxlength="10" minlength="10" required>
                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="designation" class="col-md-2 col-lg-2 col-form-label">Designation <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="designation" class="form-control" id="designation" value="<?=$designation?>" required>
                @error('designation') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="dob" class="col-md-2 col-lg-2 col-form-label">DOB <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="dob" class="form-control" id="dob" value="<?=$dob?>" max="<?= date('Y-m-d') ?>" required>
                @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            
            <div class="row mb-3">
              <label for="password" class="col-md-2 col-lg-2 col-form-label">Password <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="password" name="password" class="form-control" id="password" <?=((!empty($row))?'':'required')?>>
                <?php if($row){?><small class="text-primary">Leave blank if you dont want to update</small><?php }?>
              </div>
            </div>
            <div class="row mb-3">
              <label for="photo" class="col-md-2 col-lg-2 col-form-label">Photo <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="file" name="photo" class="form-control" id="photo" <?=((!empty($row))?'':'required')?>>
                <small class="text-info">* Only JPG, JPEG, ICO, SVG, PNG files are allowed</small><br>
                <?php if($photo != ''){?>
                  <img src="<?=env('UPLOADS_URL').'user/'.$photo?>" class="img-thumbnail" alt="<?=$name?>" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php } else {?>
                  <img src="<?=env('NO_IMAGE')?>" alt="<?=$name?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php }?>
                @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="biodata" class="col-md-2 col-lg-2 col-form-label">Biodata <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="file" name="biodata" class="form-control" id="biodata" <?=((!empty($row))?'':'required')?>>
                <small class="text-info">* Only PDF files are allowed</small><br>
                <?php if($biodata != ''){?>
                  <a href="<?=env('UPLOADS_URL').'user/'.$biodata?>" target="_blank" class="badge badge-info">View file</a>
                <?php }?>
                @error('biodata') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary"><?=(($row)?'Save':'Add')?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>