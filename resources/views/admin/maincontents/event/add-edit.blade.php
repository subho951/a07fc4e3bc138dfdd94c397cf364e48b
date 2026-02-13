<?php

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
    if ($row) {
      $title                = $row->title;
      $description          = $row->description;
      $venue                = $row->venue;
      $event_date           = $row->event_date;
      $photo                = $row->photo;
      $video                = $row->video;
      $seat_capacity        = $row->seat_capacity;
    } else {
      $title                = '';
      $description          = '';
      $venue                = '';
      $event_date           = '';
      $photo                = '';
      $video                = '';
      $seat_capacity        = '';
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
              <label for="title" class="col-md-2 col-lg-2 col-form-label">Title <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="title" class="form-control" id="title" value="<?= $title ?>" required>
                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="description" class="col-md-2 col-lg-2 col-form-label">Description <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <textarea name="description" class="form-control" id="ckeditor1"><?= $description ?></textarea>
                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="venue" class="col-md-2 col-lg-2 col-form-label">Venue <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="venue" class="form-control" id="venue" value="<?= $venue ?>" required>
                @error('venue') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="seat_capacity" class="col-md-2 col-lg-2 col-form-label">Seat Capacity <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="text" name="seat_capacity" class="form-control" id="seat_capacity" value="<?= $seat_capacity ?>" required>
                @error('seat_capacity') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="event_date" class="col-md-2 col-lg-2 col-form-label">Date <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="date" name="event_date" class="form-control" id="event_date" value="<?= $event_date ?>" required>
                @error('event_date') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="photo" class="col-md-2 col-lg-2 col-form-label">Photo <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="file" name="photo" class="form-control" id="photo" <?= ((!empty($row)) ? '' : 'required') ?>>
                <small class="text-info">* Only JPG, JPEG, ICO, SVG, PNG files are allowed</small><br>
                <?php if ($photo != '') { ?>
                  <img src="<?= env('UPLOADS_URL') . 'event/' . $photo ?>" class="img-thumbnail" alt="<?= $title ?>" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php } else { ?>
                  <img src="<?= env('NO_IMAGE') ?>" alt="<?= $title ?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                <?php } ?>
                @error('photo') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <div class="row mb-3">
              <label for="video" class="col-md-2 col-lg-2 col-form-label">Video <span class="text-danger">*</span></label>
              <div class="col-md-10 col-lg-10">
                <input type="file" name="video" class="form-control" id="video" <?= ((!empty($row)) ? '' : 'required') ?>>
                <small class="text-info">* Only mp4,mov,avi,wmv files are allowed</small><br>
                <?php if ($video != '') { ?>
                  <a href="<?= env('UPLOADS_URL') . 'event/' . $video ?>" target="_blank" class="badge badge-info">View file</a>
                <?php } ?>
                @error('video') <span class="text-danger">{{ $message }}</span> @enderror
              </div>
            </div>

            <h5>Event Questions</h5>
            <div class="field_wrapper">
              <?php if($eventQuestions){ foreach($eventQuestions as $eventQuestion){?>
                <div class="row" style="border: 1px solid #fcc312;padding: 10px;border-radius: 10px;margin-bottom: 10px;">
                  <input type="hidden" name="event_question_id[]" value="<?= $eventQuestion->id ?>">
                  <div class="col-md-4">
                    <label>Question</label>
                    <textarea class="form-control" name="event_question[]"><?= $eventQuestion->event_question ?></textarea>
                  </div>
                  <div class="col-md-2">
                    <label>Question Type</label>
                    <select class="form-control" name="event_answer_type[]" required="">
                      <option value="" selected>Select Type</option>
                      <option value="INPUTBOX" <?= (($eventQuestion->event_answer_type == 'INPUTBOX')?'selected':'') ?>>INPUTBOX</option>
                      <option value="TEXTAREA" <?= (($eventQuestion->event_answer_type == 'TEXTAREA')?'selected':'') ?>>TEXTAREA</option>
                      <option value="RADIO" <?= (($eventQuestion->event_answer_type == 'RADIO')?'selected':'') ?>>RADIO</option>
                      <option value="CHECKBOX" <?= (($eventQuestion->event_answer_type == 'CHECKBOX')?'selected':'') ?>>CHECKBOX</option>
                      <option value="DROPDOWN" <?= (($eventQuestion->event_answer_type == 'DROPDOWN')?'selected':'') ?>>DROPDOWN</option>
                      <option value="FILE" <?= (($eventQuestion->event_answer_type == 'FILE')?'selected':'') ?>>FILE</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label>Options</label>
                    <textarea class="form-control" name="event_answer_options[]"><?= $eventQuestion->event_answer_options ?></textarea>
                    <small class="text-primary">Input question options by comma separated</small>
                  </div>
                  <div class="col-md-2">
                    <label>Action</label><br>
                    <a href="javascript:void(0);" class="remove_button" title="Remove row">
                      <i class="fa fa-minus-circle fa-2x text-danger"></i>
                    </a>
                  </div>
                </div>
              <?php } }?>
              <div class="mt-3 mb-3" style="text-align: center;">
                <a href="javascript:void(0);" class="btn btn-success btn-sm add_button" title="Add row">
                  <i class="fa fa-plus-circle"></i> Add Question
                </a>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary"><?= (($row) ? 'Save' : 'Add') ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    var maxField = 10; //Input fields increment limitation
    var addButton = $('.add_button'); //Add button selector
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var fieldHTML = `<div class="row" style="border: 1px solid #fcc312;padding: 10px;border-radius: 10px;margin-bottom: 10px;">
                        <input type="hidden" name="event_question_id[]" value="0">
                        <div class="col-md-4">
                          <label>Question</label>
                          <textarea class="form-control" name="event_question[]" required></textarea>
                        </div>
                        <div class="col-md-2">
                          <label>Question Type</label>
                          <select class="form-control" name="event_answer_type[]" required">
                            <option value="" selected>Select Type</option>
                            <option value="INPUTBOX">INPUTBOX</option>
                            <option value="TEXTAREA">TEXTAREA</option>
                            <option value="RADIO">RADIO</option>
                            <option value="CHECKBOX">CHECKBOX</option>
                            <option value="DROPDOWN">DROPDOWN</option>
                            <option value="FILE">FILE</option>
                          </select>
                        </div>
                        <div class="col-md-4">
                          <label>Options</label>
                          <textarea class="form-control" name="event_answer_options[]"></textarea>
                        </div>
                        <div class="col-md-2">
                          <label>Action</label><br>
                          <a href="javascript:void(0);" class="remove_button" title="Remove row">
                            <i class="fa fa-minus-circle fa-2x text-danger"></i>
                          </a>
                        </div>
                      </div>`; //New input field html 
    var x = 1; //Initial field counter is 1

    // Once add button is clicked
    $(addButton).click(function() {
      //Check maximum number of input fields
      if (x < maxField) {
        x++; //Increase field counter
        $(wrapper).append(fieldHTML); //Add field html
      } else {
        alert('A maximum of ' + maxField + ' fields are allowed to be added. ');
      }
    });

    // Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e) {
      e.preventDefault();
      $(this).parent('div').parent('div').remove(); //Remove field html
      x--; //Decrease field counter
    });
  });
</script>