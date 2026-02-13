<?php
use App\Models\EventQuestion;
use App\Models\UserRegEvent;
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
          <h5 class="card-title pt-0">
            <a href="<?=url('admin/' . $controllerRoute . '/add/')?>" class="btn btn-outline-success btn-sm">Add <?=$module['title']?></a>
          </h5>
          <!-- Table with stripped rows -->
          <table class="table datatable global_table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Title</th>
                <th scope="col">Venue</th>
                <th scope="col">Date</th>
                <th scope="col">Questions Count</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if($rows){ $sl=1; foreach($rows as $row){?>
                <tr>
                  <th scope="row"><?=$sl++?></th>
                  <td><?=$row->title?></td>
                  <td><?=$row->venue?></td>
                  <td><?=date_format(date_create($row->event_date), "d-m-Y")?></td>
                  <td>
                    <?php
                    echo $questionCount = EventQuestion::where('event_id', '=', $row->id)->count();
                    ?>
                  </td>
                  <td>
                    <a href="<?=url('admin/' . $controllerRoute . '/edit/'.Helper::encoded($row->id))?>" class="btn btn-outline-primary btn-sm" title="Edit <?=$module['title']?>"><i class="fa fa-edit"></i></a>
                    <a href="<?=url('admin/' . $controllerRoute . '/delete/'.Helper::encoded($row->id))?>" class="btn btn-outline-danger btn-sm" title="Delete <?=$module['title']?>" onclick="return confirm('Do You Want To Delete This <?=$module['title']?>');"><i class="fa fa-trash"></i></a>
                    <?php if($row->status){?>
                      <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-success btn-sm" title="Activate <?=$module['title']?>"><i class="fa fa-check"></i></a>
                    <?php } else {?>
                      <a href="<?=url('admin/' . $controllerRoute . '/change-status/'.Helper::encoded($row->id))?>" class="btn btn-outline-warning btn-sm" title="Deactivate <?=$module['title']?>"><i class="fa fa-times"></i></a>
                    <?php }?>
                    <br><br>
                    <?php
                    $userCount = UserRegEvent::where('eventid', '=', $row->id)->where('status', '=', 1)->count();
                    ?>
                    <a target="_blank" href="<?=url('admin/' . $controllerRoute . '/registered-users/'.Helper::encoded($row->id))?>" class="btn btn-info btn-sm" title="<?=$module['title']?> Registered Users"><i class="fa fa-users"></i> Registered Users (<?= $userCount ?>)</a>
                  </td>
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