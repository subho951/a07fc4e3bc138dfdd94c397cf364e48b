<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
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
          <table class="table datatable global_table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Designation</th>
                <th scope="col">Photo</th>
                <th scope="col">Points</th>
              </tr>
            </thead>
            <tbody>
              <?php if($rows){ $sl=1; foreach($rows as $row){?>
                <tr>
                  <th scope="row"><?=$sl++?></th>
                  <td><?=$row->name?></td>
                  <td><?=$row->email?></td>
                  <td><?=$row->phone?></td>
                  <td><?=$row->designation?></td>
                  <td>
                    <?php if($row->photo != ''){?>
                      <img src="<?=env('UPLOADS_URL').'user/'.$row->photo?>" class="img-thumbnail" alt="<?=$row->name?>" style="width: 120px; height: 120px; margin-top: 10px; border-radius:50%;">
                    <?php } else {?>
                      <img src="<?=env('NO_IMAGE')?>" alt="<?=$row->name?>" class="img-thumbnail" style="width: 120px; height: 120px; margin-top: 10px; border-radius:50%;">
                    <?php }?>
                  </td>
                  <td><?=$row->points?></td>
                </tr>
              <?php } }?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->
        </div>
      </div>
    </div>
  </div>
</section>