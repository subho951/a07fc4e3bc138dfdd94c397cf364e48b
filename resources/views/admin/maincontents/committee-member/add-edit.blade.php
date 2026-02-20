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
      $committee_category_id              = $row->committee_category_id;
      $member_id                          = $row->id;
      $committee_member_type              = $row->committee_member_type;
    } else {
      $committee_category_id              = '';
      $member_id                          = '';
      $committee_member_type              = '';
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
              <label for="committee_category_id" class="col-md-2 col-lg-2 col-form-label">Committee Category <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <select name="committee_category_id" class="form-control" id="committee_category_id" required>
                    <option value="" selected>Select Committee Category</option>
                  <?php if($cats){ foreach($cats as $cat){?>
                    <option value="<?= $cat->id?>" <?= (($committee_category_id == $cat->id)?'selected':'') ?>><?= $cat->name?></option>
                  <?php } }?>
                </select>
                @error('committee_category_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="member_id" class="col-md-2 col-lg-2 col-form-label">Member <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <select name="member_id" class="form-control" id="member_id" required>
                    <option value="" selected>Select Member</option>
                  <?php if($members){ foreach($members as $member){?>
                    <option value="<?= $member->id?>" <?= (($member_id == $member->id)?'selected':'') ?>><?= $member->name?></option>
                  <?php } }?>
                </select>
                @error('member_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="committee_member_type" class="col-md-2 col-lg-2 col-form-label">Type <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="radio" name="committee_member_type" id="type1" value="1" <?= (($committee_member_type == 1)?'checked':'') ?> required>&nbsp;<label for="type1">Committee Members</label>
                <input type="radio" name="committee_member_type" id="type2" value="0" <?= (($committee_member_type == 0)?'checked':'') ?> required>&nbsp;<label for="type2">Sub Committee Members</label>
                @error('committee_member_type') <span class="text-danger">{{ $message }}</span> @enderror
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