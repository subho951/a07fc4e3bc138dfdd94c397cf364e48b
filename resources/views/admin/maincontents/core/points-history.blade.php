<?php
use App\Models\Event;
use App\Models\CoreMeeting;
use App\Helpers\Helper;

$controllerRoute = $module['controller_route'];
?>
<div class="pagetitle">
    <h1><?= $page_header ?></h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= url('admin/' . $controllerRoute . '/list/') ?>"><?= $module['title'] ?> List</a></li>
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
                    <h5 class="card-title pt-0">
                        Total Points : <?= (($core)?$core->points:'') ?>
                    </h5>
                    <table class="table datatable global_table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Event</th>
                                <th scope="col">Meeting</th>
                                <th scope="col">Date</th>
                                <th scope="col">Credited Points</th>
                                <th scope="col">Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rows) {
                                $sl = 1;
                                foreach ($rows as $row) { ?>
                                    <tr>
                                        <th scope="row"><?= $sl++ ?></th>
                                        <td>
                                            <?php
                                            $getEvent = Event::select('title', 'venue', 'event_date')->where('id', '=', $row->event_id)->first();
                                            ?>
                                            <span><?= (($getEvent)?$getEvent->title:'') ?></span><br>
                                            <small><?= (($getEvent)?$getEvent->venue:'') ?></small><br>
                                            <small><?= (($getEvent)?date_format(date_create($getEvent->event_date), "d.m.Y"):'') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $getMeeting = CoreMeeting::select('meeting_type', 'venue', 'from_date', 'to_date')->where('id', '=', $row->meeting_id)->first();
                                            ?>
                                            <span><?= (($getMeeting)?$getMeeting->meeting_type:'') ?></span><br>
                                            <small><?= (($getMeeting)?$getMeeting->venue:'') ?></small><br>
                                            <small><?= (($getMeeting)?date_format(date_create($getMeeting->from_date), "d.m.Y"):'') ?> - <?= (($getMeeting)?date_format(date_create($getMeeting->to_date), "d.m.Y"):'') ?></small>
                                        </td>
                                        <td><?= date_format(date_create($row->created_at), "d-m-Y h:i A") ?></td>
                                        <td><?= $row->credited_points ?></td>
                                        <td><?= $row->note ?></td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                    <!-- End Table with stripped rows -->
                </div>
            </div>
        </div>
    </div>
</section>