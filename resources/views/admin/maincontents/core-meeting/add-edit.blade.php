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
      $core_id                    = $row->core_id;
      $meeting_type               = $row->meeting_type;
      $from_date                  = $row->from_date;
      $to_date                    = $row->to_date;
      $venue                      = $row->venue;
      $short_description          = $row->short_description;
      $attendance                 = $row->attendance;
      $quorum_percent             = $row->quorum_percent;
    } else {
      $core_id                    = '';
      $meeting_type               = '';
      $from_date                  = '';
      $to_date                    = '';
      $venue                      = '';
      $short_description          = '';
      $attendance                 = 0;
      $quorum_percent             = 0;
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
              <label for="core_id" class="col-md-2 col-lg-2 col-form-label">Core <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <select name="core_id" class="form-control" id="core_id" required>
                    <option value="" selected>Select Core</option>
                  <?php if($cores){ foreach($cores as $core){?>
                    <option value="<?= $core->id?>" <?= (($core->id == $core_id)?'selected':'') ?>><?= $core->name?></option>
                  <?php } }?>
                </select>
                @error('core_id') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="meeting_type" class="col-md-2 col-lg-2 col-form-label">Meeting Type <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <select name="meeting_type" class="form-control" id="meeting_type" required>
                    <option value="" selected>Select Meeting Type</option>
                    <option value="CORE MEETING" <?= (($meeting_type == 'CORE MEETING')?'selected':'') ?>>CORE MEETING</option>
                    <option value="LOCAL INBOUND" <?= (($meeting_type == 'LOCAL INBOUND')?'selected':'') ?>>LOCAL INBOUND</option>
                    <option value="OUTBOUND" <?= (($meeting_type == 'OUTBOUND')?'selected':'') ?>>OUTBOUND</option>
                </select>
                @error('meeting_type') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="from_date" class="col-md-2 col-lg-2 col-form-label">From Date <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="from_date" class="form-control" id="from_date" value="<?=$from_date?>" required>
                @error('from_date') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="to_date" class="col-md-2 col-lg-2 col-form-label">To Date <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="to_date" class="form-control" id="to_date" value="<?=$to_date?>" required>
                @error('to_date') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="venue" class="col-md-2 col-lg-2 col-form-label">Venue <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="venue" class="form-control" id="venue" value="<?=$venue?>" required>
                @error('venue') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="short_description" class="col-md-2 col-lg-2 col-form-label">Short Description</label>
              <div class="col-md-10 col-lg-10">
                <textarea name="short_description" class="form-control" id="short_description"><?=$short_description?></textarea>
                @error('short_description') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="attendance" class="col-md-2 col-lg-2 col-form-label">Attendance</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="attendance" class="form-control" id="attendance" value="<?=$attendance?>">
                @error('attendance') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>
            <div class="row mb-3">
              <label for="quorum_percent" class="col-md-2 col-lg-2 col-form-label">Quorum Percent</label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="quorum_percent" class="form-control" id="quorum_percent" value="<?=$quorum_percent?>">
                @error('quorum_percent') <span class="text-danger">{{ $message }}</span> @enderror
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