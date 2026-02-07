<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
$current_url = url()->current();
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
      $id                         = $row->id;
      $page_title                 = $row->page_title;
      $slug                       = $row->slug;
      $short_description          = $row->short_description;
      $long_description           = $row->long_description;
      $meta_title                 = $row->meta_title;
      $meta_description           = $row->meta_description;
      $meta_keywords              = $row->meta_keywords;
      $page_banner_image          = $row->page_banner_image;
    } else {
      $id                         = '';
      $page_title                 = '';
      $slug                       = '';
      $short_description          = '';
      $long_description           = '';
      $meta_title                 = '';
      $meta_description           = '';
      $meta_keywords              = '';
      $page_banner_image          = '';
    }
    ?>
    <div class="col-xl-12">
      <div class="card">
        <div class="card-body pt-3">
          <form method="POST" action="" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
              <label for="page_title" class="col-md-2 col-lg-2 col-form-label">Page Title</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="page_title" class="form-control" id="page_title" rows="5" value="<?=$page_title?>" required>
              </div>
            </div>
            <!-- <div class="row mb-3">
              <label for="short_description" class="col-md-2 col-lg-2 col-form-label">Short Description</label>
              <div class="col-md-10 col-lg-10">
                <textarea type="text" name="short_description" class="form-control" id="ckeditor1" rows="5" required><?=$short_description?></textarea>
              </div>
            </div> -->
            <div class="row mb-3">
              <label for="long_description" class="col-md-2 col-lg-2 col-form-label">Description</label>
              <div class="col-md-10 col-lg-10">
                <textarea type="text" name="long_description" class="form-control" id="ckeditor2" rows="5"><?=$long_description?></textarea>
              </div>
            </div>
            <!-- <div class="row mb-3">
              <label for="page_banner_image" class="col-md-2 col-lg-2 col-form-label">Page Banner Image</label>
              <div class="col-md-10 col-lg-10">
                <input type="file" name="page_banner_image" class="form-control" id="page_banner_image">
                <small class="text-info">* Only JPG, JPEG, ICO, SVG, PNG, WEBP, AVIF files are allowed</small><br>
                <?php if($page_banner_image != ''){?>
                  <img src="<?=env('UPLOADS_URL').'page/'.$page_banner_image?>" class="img-thumbnail" alt="<?=$page_title?>" style="width: 150px; height: 150px; margin-top: 10px;">
                  <div class="pt-2">
                    <a href="<?=url('admin/common-delete-image/'.Helper::encoded($current_url).'/pages/page_banner_image/id/'.$id)?>" class="btn btn-danger btn-sm" title="Remove image" onclick="return confirm('Do You Want To Delete This Image ?');"><i class="bi bi-trash"></i></a>
                  </div>
                <?php } else {?>
                  <img src="<?=env('NO_IMAGE')?>" alt="<?=$page_title?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php }?>
              </div>
            </div> -->
            <div class="row mb-3">
              <label for="meta_title" class="col-md-2 col-lg-2 col-form-label">Meta Title</label>
              <div class="col-md-10 col-lg-10">
                <textarea type="text" name="meta_title" class="form-control" id="meta_title" rows="5"><?=$meta_title?></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <label for="meta_description" class="col-md-2 col-lg-2 col-form-label">Meta Description</label>
              <div class="col-md-10 col-lg-10">
                <textarea type="text" name="meta_description" class="form-control" id="meta_description" rows="5"><?=$meta_description?></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <label for="meta_keywords" class="col-md-2 col-lg-2 col-form-label">Meta Keywords</label>
              <div class="col-md-10 col-lg-10">
                <textarea type="text" name="meta_keywords" class="form-control" id="meta_keywords" rows="5"><?=$meta_keywords?></textarea>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>