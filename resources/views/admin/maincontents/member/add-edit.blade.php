<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<!-- Bootstrap CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<style type="text/css">
    /* highlight searched value */
    /* .highlight {
    background-color: orange;
    padding: 0 1px;
    border-radius: 3px;
    } */

    .choices__list--multiple .choices__item {
        background-color: #48974e;
        border: 1px solid #48974e;
    }
</style>
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
      $company_name       = $row->company_name;
      $designation        = $row->designation;
      $dob                = $row->dob;
      $doj                = $row->doj;
      $doa                = $row->doa;
      $core_id            = $row->core_id;
      $spouse_name        = $row->spouse_name;
      $profession         = $row->profession;
      $alumni             = $row->alumni;
      $industry_id        = (($row->industry_id != '')?json_decode($row->industry_id):[]);
      $interest_id        = (($row->interest_id != '')?json_decode($row->interest_id):[]);
      $address            = $row->address;
    } else {
      $name               = '';
      $email              = '';
      $phone              = '';
      $photo              = '';
      $company_name       = '';
      $designation        = '';
      $dob                = '';
      $doj                = '';
      $doa                = '';
      $core_id            = '';
      $spouse_name        = '';
      $profession         = '';
      $alumni             = '';
      $industry_id        = [];
      $interest_id        = [];
      $address            = '';
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
              <label for="company_name" class="col-md-2 col-lg-2 col-form-label">Company Name <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="company_name" class="form-control" id="company_name" value="<?=$company_name?>" required>
                @error('company_name') <span class="text-danger">{{ $message }}</span> @enderror
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
              <label for="dob" class="col-md-2 col-lg-2 col-form-label">Date of Birth <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="dob" class="form-control" id="dob" value="<?=$dob?>" max="<?= date('Y-m-d') ?>" required>
                @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="doj" class="col-md-2 col-lg-2 col-form-label">Member Since <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <select name="doj" class="form-control" id="doj" required>
                  <option value="" selected>Member Since</option>
                  <?php for($k=1980;$k<date('Y');$k++){?>
                    <option value="<?= $k?>" <?= (($doj == $k)?'selected':'') ?>><?= $k?></option>
                  <?php }?>
                </select>
                @error('doj') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="doa" class="col-md-2 col-lg-2 col-form-label">Date of Anniversary</label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="doa" class="form-control" id="doa" value="<?=$doa?>" max="<?= date('Y-m-d') ?>">
                @error('doa') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="core_id" class="col-md-2 col-lg-2 col-form-label">Core</label>
              <div class="col-md-10 col-lg-10">
                <select name="core_id" class="form-control" id="core_id">
                    <option value="" selected>Select Core</option>
                  <?php if($cores){ foreach($cores as $core){?>
                    <option value="<?= $core->id?>" <?= (($core->id == $core_id)?'selected':'') ?>><?= $core->name?></option>
                  <?php } }?>
                </select>
                @error('core_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="spouse_name" class="col-md-2 col-lg-2 col-form-label">Spouse Name</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="spouse_name" class="form-control" id="spouse_name" value="<?=$spouse_name?>">
                @error('spouse_name') <span class="text-danger">{{ $message }}</span> @enderror
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
              <label for="profession" class="col-md-2 col-lg-2 col-form-label">Profession <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="profession" class="form-control" id="profession" value="<?=$profession?>" required>
                @error('profession') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="alumni" class="col-md-2 col-lg-2 col-form-label">Alumni</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="alumni" class="form-control" id="alumni" value="<?=$alumni?>">
                @error('alumni') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="industry_id" class="col-md-2 col-lg-2 col-form-label">Industry</label>
              <div class="col-md-10 col-lg-10">
                <select name="industry_id[]" class="form-control" id="choices-multiple-remove-button" multiple>
                  <?php if($industries){ foreach($industries as $industry){?>
                    <option value="<?= $industry->id?>" <?= ((in_array($industry->id, $industry_id))?'selected':'') ?>><?= $industry->name?></option>
                  <?php } }?>
                </select>
                @error('industry_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="interest_id" class="col-md-2 col-lg-2 col-form-label">Interest</label>
              <div class="col-md-10 col-lg-10">
                <select name="interest_id[]" class="form-control" id="choices-multiple-remove-button" multiple>
                  <?php if($interests){ foreach($interests as $interest){?>
                    <option value="<?= $interest->id?>" <?= ((in_array($interest->id, $interest_id))?'selected':'') ?>><?= $interest->name?></option>
                  <?php } }?>
                </select>
                @error('interest_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="address" class="col-md-2 col-lg-2 col-form-label">Address <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="address" class="form-control" id="address" value="<?=$address?>" required>
                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
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
<script type="text/javascript">
    $(document).ready(function() {
        var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
            removeItemButton: true,
            maxItemCount: 30,
            searchResultLimit: 30,
            renderChoiceLimit: 30
        });
    });
</script>