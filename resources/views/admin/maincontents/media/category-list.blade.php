<?php

use App\Helpers\Helper;

$controllerRoute = $module['controller_route'];
?>
<div class="pagetitle">
  <h1><?= $page_header ?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
      <li class="breadcrumb-item"><a href="<?= url('admin/media/institute-list') ?>">Institute List</a></li>
      <li class="breadcrumb-item active"><?= $page_header ?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section">
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
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <!-- <h5 class="card-title pt-0">
            <a href="<?= url('admin/' . $controllerRoute . '/add/') ?>" class="btn btn-outline-success btn-sm">Add <?= $module['title'] ?></a>
          </h5> -->
          <div class="row">
            <?php if ($cats) { foreach ($cats as $cat) { ?>
                <div class="col-md-4">
                  <a href="<?=url('admin/' . $controllerRoute . '/media-list/'.Helper::encoded($institute_id).'/'.Helper::encoded($cat->id))?>">
                    <div class="card">
                      <div class="card-body">
                        <?= $cat->name ?>
                      </div>
                    </div>
                  </a>
                </div>
            <?php } } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>