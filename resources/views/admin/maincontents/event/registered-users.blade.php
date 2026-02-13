<?php
use App\Models\EventQuestion;
use App\Models\UserRegEvent;
use App\Models\UserRegEventAnswer;
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
    <?php
    $regUserCount = UserRegEvent::where('eventid', '=', $event_id)->where('status', '=', 1)->count();
    $regAttendedUserCount = UserRegEvent::where('eventid', '=', $event_id)->where('status', '=', 1)->where('entry_timestamp', '!=', NULL)->count();
    ?>
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Registered Users</h5>
                        <h4 style="font-weight: bold;font-size: 40px;"><?= $regUserCount ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Attended Users</h5>
                        <h4 style="font-weight: bold;font-size: 40px;"><?= $regAttendedUserCount ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Not Attended Users</h5>
                        <h4 style="font-weight: bold;font-size: 40px;"><?= ($regUserCount - $regAttendedUserCount) ?></h4>
                    </div>
                </div>
            </div>
          </div>

          <table class="table global_table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Profile</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <?php if($eventQuestions){ foreach($eventQuestions as $eventQuestion){?>
                    <th scope="col"><?= $eventQuestion->event_question ?></th>
                <?php } }?>
                <th scope="col">Event QR Code</th>
                <th scope="col">Event Registered On</th>
                <th scope="col">Event Entry On</th>
              </tr>
            </thead>
            <tbody>
              <?php if(count($eventUsers) > 0){ $sl=1; foreach($eventUsers as $row){?>
                <tr>
                  <th scope="row"><?=$sl++?></th>
                  <td>
                    <?php if($row->user_photo != ''){?>
                      <img src="<?=env('UPLOADS_URL').'user/'.$row->user_photo?>" class="img-thumbnail" alt="<?=$row->user_name?>" style="width: 75px; height: 75px; margin-top: 10px; border-radius:50%;">
                    <?php } else {?>
                      <img src="<?=env('NO_IMAGE')?>" alt="<?=$row->user_name?>" class="img-thumbnail" style="width: 75px; height: 75px; margin-top: 10px; border-radius:50%;">
                    <?php }?>
                  </td>
                  <td><?=$row->user_name?></td>
                  <td><?=$row->user_email?></td>
                  <td><?=$row->user_phone?></td>
                  <?php if($eventQuestions){ foreach($eventQuestions as $eventQuestion){?>
                    <td>
                        <?php
                        $getAnswer = UserRegEventAnswer::select('event_answer')->where('eventid', '=', $row->eventid)->where('userid', '=', $row->userid)->where('event_question_id', '=', $eventQuestion->id)->first();
                        echo (($getAnswer)?$getAnswer->event_answer:'');
                        ?>
                    </td>
                  <?php } }?>
                  <td>
                    <?php if($row->qrcode != ''){?>
                      <img src="<?=$row->qrcode?>" class="img-thumbnail" alt="<?=$row->user_name?>" style="width: 75px; height: 75px; margin-top: 10px;">
                    <?php }?>
                  </td>
                  <td><?=date_format(date_create($row->date), "d-m-Y")?> <?=date_format(date_create($row->time), "h:i a")?></td>
                  <td>
                    <?php if($row->entry_timestamp != ''){?>
                        <span class="badge bg-success">ATTENDED</span><br>
                        <small><?=(($row->entry_timestamp != '')?date_format(date_create($row->entry_timestamp), "d-m-Y h:i a"):'')?></small>
                    <?php } else {?>
                        <span class="badge bg-danger">NOT ATTENDED</span>
                    <?php }?>
                    </td>
                </tr>
              <?php } } else {?>
                <tr>
                    <td colspan="10" style="color:red; text-align:center;">No registered users found</td>
                </tr>
              <?php }?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->
        </div>
      </div>
    </div>
  </div>
</section>