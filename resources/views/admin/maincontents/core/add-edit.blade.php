<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<style type="text/css">
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
      $description        = $row->description;
      $photo              = $row->photo;
    } else {
      $name               = '';
      $description        = '';
      $photo              = '';
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
              <label for="description" class="col-md-2 col-lg-2 col-form-label">Description <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <textarea name="description" class="form-control" id="description" rows="3" required><?=$description?></textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="photo" class="col-md-2 col-lg-2 col-form-label">Photo <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="file" name="photo" class="form-control" id="photo" <?=((!empty($row))?'':'required')?>>
                <small class="text-info">* Only JPG, JPEG, ICO, SVG, PNG files are allowed</small><br>
                <?php if($photo != ''){?>
                  <img src="<?=env('UPLOADS_URL').'core/'.$photo?>" class="img-thumbnail" alt="<?=$name?>" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php } else {?>
                  <img src="<?=env('NO_IMAGE')?>" alt="<?=$name?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php }?>
                @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="member_id" class="col-md-2 col-lg-2 col-form-label">Members <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <select class="form-control" name="member_id[]" id="choices-multiple-remove-button" multiple required>
                  <?php if ($members) { foreach ($members as $member) { ?>
                          <option value="<?= $member->id ?>" <?= ((in_array($member->id, $memberIDs))?'selected':'') ?>><?= $member->name ?> (<?= (($member->type == 1)?'committee member':'normal member') ?>)</option>
                  <?php } } ?>
                </select>
                @error('member_id') <span class="text-danger">{{ $message }}</span> @enderror
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
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