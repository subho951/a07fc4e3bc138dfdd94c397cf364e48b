<?php
use App\Helpers\Helper;
?>
<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('dashboard')?>">Home</a></li>
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
          <ul class="nav nav-tabs nav-tabs-bordered">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab2">Success Login</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab1">Falied Login</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab3">Logout</button>
            </li>
          </ul>
          <div class="tab-content pt-2">
            <div class="tab-pane fade show active profile-overview" id="tab2">
              <!-- Table with stripped rows -->
              <table class="table datatable global_table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">User Type</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">Activity Details</th>
                    <th scope="col">Activity Date</th>
                    <th scope="col">Activity Type</th>
                    <th scope="col">Platform</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if($rows2){ $sl=1; foreach($rows2 as $row){?>
                    <tr>
                      <th scope="row"><?=$sl++?></th>
                      <td><?=$row->user_type?></td>
                      <td><?=$row->user_name?></td>
                      <td><?=$row->user_email?></td>
                      <td><?=$row->ip_address?></td>
                      <td><?=$row->activity_details?></td>
                      <td><?=date_format(date_create($row->created_at), "M d, Y h:i A")?></td>
                      <td>
                        <?php if($row->activity_type == 0) {?>
                          <span class="badge bg-danger">FAILED</span>
                        <?php } elseif($row->activity_type == 1) {?>
                          <span class="badge bg-success">SUCCESS</span>
                        <?php } elseif($row->activity_type == 2) {?>
                          <span class="badge bg-primary">Log Out</span>
                        <?php }?>
                      </td>
                      <td><?=$row->platform_type?></td>
                    </tr>
                  <?php } }?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
            <div class="tab-pane fade show profile-overview" id="tab1">
              <!-- Table with stripped rows -->
              <table class="table datatable global_table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">User Type</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">Activity Details</th>
                    <th scope="col">Activity Date</th>
                    <th scope="col">Activity Type</th>
                    <th scope="col">Platform</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if($rows1){ $sl=1; foreach($rows1 as $row){?>
                    <tr>
                      <th scope="row"><?=$sl++?></th>
                      <td><?=$row->user_type?></td>
                      <td><?=$row->user_name?></td>
                      <td><?=$row->user_email?></td>
                      <td><?=$row->ip_address?></td>
                      <td><?=$row->activity_details?></td>
                      <td><?=date_format(date_create($row->created_at), "M d, Y h:i A")?></td>
                      <td>
                        <?php if($row->activity_type == 0) {?>
                          <span class="badge bg-danger">FAILED</span>
                        <?php } elseif($row->activity_type == 1) {?>
                          <span class="badge bg-success">SUCCESS</span>
                        <?php } elseif($row->activity_type == 2) {?>
                          <span class="badge bg-primary">Log Out</span>
                        <?php }?>
                      </td>
                      <td><?=$row->platform_type?></td>
                    </tr>
                  <?php } }?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
            <div class="tab-pane fade show profile-overview" id="tab3">
              <!-- Table with stripped rows -->
              <table class="table datatable global_table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">User Type</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">Activity Details</th>
                    <th scope="col">Activity Date</th>
                    <th scope="col">Activity Type</th>
                    <th scope="col">Platform</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if($rows3){ $sl=1; foreach($rows3 as $row){?>
                    <tr>
                      <th scope="row"><?=$sl++?></th>
                      <td><?=$row->user_type?></td>
                      <td><?=$row->user_name?></td>
                      <td><?=$row->user_email?></td>
                      <td><?=$row->ip_address?></td>
                      <td><?=$row->activity_details?></td>
                      <td><?=date_format(date_create($row->created_at), "M d, Y h:i A")?></td>
                      <td>
                        <?php if($row->activity_type == 0) {?>
                          <span class="badge bg-danger">FAILED</span>
                        <?php } elseif($row->activity_type == 1) {?>
                          <span class="badge bg-success">SUCCESS</span>
                        <?php } elseif($row->activity_type == 2) {?>
                          <span class="badge bg-primary">Log Out</span>
                        <?php }?>
                      </td>
                      <td><?=$row->platform_type?></td>
                    </tr>
                  <?php } }?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>