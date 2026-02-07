<?php
$user_type = session('type');
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
    <div class="col-xl-2">
      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <?php if($admin->image != ''){?>
            <img src="<?=env('UPLOADS_URL').$admin->image?>" alt="<?=$admin->name?>" class="rounded-circle">
          <?php } else {?>
            <img src="<?=env('NO_IMAGE')?>" alt="<?=$admin->name?>" class="img-thumbnail" class="rounded-circle" style="width: 150px; height: 150px; margin-top: 10px;">
          <?php }?>
          <h2><?=session('name')?></h2>
          <h3><?=session('type')?></h3>
        </div>
      </div>
    </div>
    <div class="col-xl-10">
      <div class="card">
        <div class="card-body pt-3">
          <!-- Bordered Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1">Profile</button>
            </li>
            <?php if($user_type == 'ma'){?>
              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab2">General</button>
              </li>
            <?php }?>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab3">Change Password</button>
            </li>
            <?php if($user_type == 'ma'){?>
              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab4">Email</button>
              </li>
              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab9">Email Templates</button>
              </li>
              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab7">SEO</button>
              </li>
            <?php }?>
          </ul>
          <div class="tab-content pt-2">
            <div class="tab-pane fade show active profile-overview" id="tab1">
              <!-- profile settings Form -->
              <form method="POST" action="{{ url('admin/profile-settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                  <label for="name" class="col-md-4 col-lg-3 col-form-label">Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="name" class="form-control" id="name" value="<?=$admin->name?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="email" class="form-control" id="email" value="<?=$admin->email?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="mobile" class="col-md-4 col-lg-3 col-form-label">Mobile</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="mobile" class="form-control" id="mobile" value="<?=$admin->mobile?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="image" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="file" name="image" class="form-control" id="profile_image">
                    <small class="text-info">* Only JPG, JPEG, ICO, SVG, PNG files are allowed</small><br>
                    <?php if($admin->image != ''){?>
                      <img src="<?=env('UPLOADS_URL').$admin->image?>" alt="<?=$admin->name?>" style="width: 150px; height: 150px; margin-top: 10px;">
                    <?php } else {?>
                      <img src="<?=env('NO_IMAGE')?>" alt="<?=$admin->name?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                    <?php }?>
                    
                    <!-- <div class="pt-2">
                      <a href="#profile_image" class="btn btn-primary btn-sm" title="Upload new profile image"><i class="bi bi-upload"></i></a>
                      <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                    </div> -->
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form><!-- End profile settings Form -->
            </div>
            <div class="tab-pane fade profile-edit pt-3" id="tab2">
              <!-- general settings Form -->
              <form method="POST" action="{{ url('admin/general-settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                  <label for="site_name" class="col-md-4 col-lg-3 col-form-label">Site Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="site_name" type="text" class="form-control" id="site_name" value="<?=$setting->site_name?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="site_phone" class="col-md-4 col-lg-3 col-form-label">Site Phone</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="site_phone" type="text" class="form-control" id="site_phone" value="<?=$setting->site_phone?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="site_mail" class="col-md-4 col-lg-3 col-form-label">Site Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="site_mail" type="email" class="form-control" id="site_mail" value="<?=$setting->site_mail?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="system_email" class="col-md-4 col-lg-3 col-form-label">System Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="system_email" type="email" class="form-control" id="system_email" value="<?=$setting->system_email?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="site_url" class="col-md-4 col-lg-3 col-form-label">Site URL</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="site_url" type="url" class="form-control" id="site_url" value="<?=$setting->site_url?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="description" class="col-md-4 col-lg-3 col-form-label">Address</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea name="description" class="form-control" id="description" rows="5"><?=$setting->description?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="theme_color" class="col-md-4 col-lg-3 col-form-label">Theme Color</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="theme_color" type="color" class="form-control" id="theme_color" value="<?=$setting->theme_color?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="font_color" class="col-md-4 col-lg-3 col-form-label">Font Color</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="font_color" type="color" class="form-control" id="font_color" value="<?=$setting->font_color?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="twitter_profile" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="twitter_profile" type="text" class="form-control" id="twitter_profile" value="<?=$setting->twitter_profile?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="facebook_profile" class="col-md-4 col-lg-3 col-form-label">Facebook Profile</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="facebook_profile" type="text" class="form-control" id="facebook_profile" value="<?=$setting->facebook_profile?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="instagram_profile" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="instagram_profile" type="text" class="form-control" id="instagram_profile" value="<?=$setting->instagram_profile?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="youtube_profile" class="col-md-4 col-lg-3 col-form-label">Youtube Profile</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="youtube_profile" type="text" class="form-control" id="youtube_profile" value="<?=$setting->youtube_profile?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="document_size" class="col-md-4 col-lg-3 col-form-label">Document Size (KB)</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="document_size" type="number" min="0" class="form-control" id="document_size" value="<?=$setting->document_size?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="photo_size" class="col-md-4 col-lg-3 col-form-label">Photo Size (KB)</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="photo_size" type="number" min="0" class="form-control" id="photo_size" value="<?=$setting->photo_size?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="video_size" class="col-md-4 col-lg-3 col-form-label">Video Size (KB)</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="video_size" type="number" min="0" class="form-control" id="video_size" value="<?=$setting->video_size?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="site_logo" class="col-md-4 col-lg-3 col-form-label">Logo</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="file" name="site_logo" class="form-control" id="site_logo">
                    <small class="text-info">* Only jpg, jpeg, png, ico files are allowed</small><br>
                    <?php if($setting->site_logo != ''){?>
                      <img src="<?=env('UPLOADS_URL').$setting->site_logo?>" alt="<?=$setting->site_name?>">
                    <?php } else {?>
                      <img src="<?=env('NO_IMAGE')?>" alt="<?=$setting->site_name?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                    <?php }?>
                    
                    <!-- <div class="pt-2">
                      <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Remove Image"><i class="bi bi-trash"></i></a>
                    </div> -->
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="site_footer_logo" class="col-md-4 col-lg-3 col-form-label">Footer Logo</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="file" name="site_footer_logo" class="form-control" id="site_footer_logo">
                    <small class="text-info">* Only jpg, jpeg, png, ico files are allowed</small><br>
                    <?php if($setting->site_footer_logo != ''){?>
                      <img src="<?=env('UPLOADS_URL').$setting->site_footer_logo?>" alt="<?=$setting->site_name?>">
                    <?php } else {?>
                      <img src="<?=env('NO_IMAGE')?>" alt="<?=$setting->site_name?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                    <?php }?>
                    
                    <!-- <div class="pt-2">
                      <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Remove Image"><i class="bi bi-trash"></i></a>
                    </div> -->
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="site_favicon" class="col-md-4 col-lg-3 col-form-label">Favicon</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="file" name="site_favicon" class="form-control" id="site_favicon">
                    <small class="text-info">* Only jpg, jpeg, png, ico files are allowed</small><br>
                    <?php if($setting->site_favicon != ''){?>
                      <img src="<?=env('UPLOADS_URL').$setting->site_favicon?>" alt="<?=$setting->site_name?>">
                    <?php } else {?>
                      <img src="<?=env('NO_IMAGE')?>" alt="<?=$setting->site_name?>" class="img-thumbnail" style="width: 150px; height: 150px; margin-top: 10px;">
                    <?php }?>
                    
                    <!-- <div class="pt-2">
                      <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Remove Image"><i class="bi bi-trash"></i></a>
                    </div> -->
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form><!-- End general settings Form -->
            </div>
            <div class="tab-pane fade pt-3" id="tab3">
              <!-- chnage password Form -->
              <form method="POST" action="{{ url('admin/change-password') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                  <label for="old_password" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="password" name="old_password" class="form-control" id="old_password">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="new_password" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="password" name="new_password" class="form-control" id="new_password">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="confirm_password" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password">
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form><!-- End chnage password Form -->
            </div>
            <div class="tab-pane fade pt-3" id="tab4">
              <!-- email settings Form -->
              <form method="POST" action="{{ url('admin/email-settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                  <label for="from_email" class="col-md-4 col-lg-3 col-form-label">From Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="from_email" class="form-control" id="from_email" value="<?=$setting->from_email?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="from_name" class="col-md-4 col-lg-3 col-form-label">From Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="from_name" class="form-control" id="from_name" value="<?=$setting->from_name?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="smtp_host" class="col-md-4 col-lg-3 col-form-label">SMTP Host</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="smtp_host" class="form-control" id="smtp_host" value="<?=$setting->smtp_host?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="smtp_username" class="col-md-4 col-lg-3 col-form-label">SMTP Username</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="smtp_username" class="form-control" id="smtp_username" value="<?=$setting->smtp_username?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="smtp_password" class="col-md-4 col-lg-3 col-form-label">SMTP Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="smtp_password" class="form-control" id="smtp_password" value="<?=$setting->smtp_password?>">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="smtp_port" class="col-md-4 col-lg-3 col-form-label">SMTP Port</label>
                  <div class="col-md-8 col-lg-9">
                    <input type="text" name="smtp_port" class="form-control" id="smtp_port" value="<?=$setting->smtp_port?>">
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form><!-- End email settings Form -->
              <a href="<?=url('admin/test-email')?>" class="btn btn-success btn-sm">Test Email</a>
            </div>
            <div class="tab-pane fade pt-3" id="tab9">
              <!-- seo settings Form -->
              <form method="POST" action="{{ url('admin/email-template') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                  <label for="email_template_user_signup" class="col-md-4 col-lg-3 col-form-label">User Signup</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="email_template_user_signup" class="form-control ckeditor" id="ckeditor1" rows="5"><?=$setting->email_template_user_signup?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="email_template_forgot_password" class="col-md-4 col-lg-3 col-form-label">Forgot Password</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="email_template_forgot_password" class="form-control ckeditor" id="ckeditor2" rows="5"><?=$setting->email_template_forgot_password?></textarea>
                  </div>
                  <!-- <div id="editor">
                    <p>Hello from CKEditor 5!</p>
                  </div> -->
                </div>
                <div class="row mb-3">
                  <label for="email_template_change_password" class="col-md-4 col-lg-3 col-form-label">Change Password</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="email_template_change_password" class="form-control ckeditor" id="ckeditor3" rows="5"><?=$setting->email_template_change_password?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="email_template_failed_login" class="col-md-4 col-lg-3 col-form-label">Failed Login</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="email_template_failed_login" class="form-control ckeditor" id="ckeditor4" rows="5"><?=$setting->email_template_failed_login?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="email_template_contactus" class="col-md-4 col-lg-3 col-form-label">Contact Us</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="email_template_contactus" class="form-control ckeditor" id="ckeditor5" rows="5"><?=$setting->email_template_contactus?></textarea>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form><!-- End seo settings Form -->
            </div>
            <div class="tab-pane fade pt-3" id="tab7">
              <!-- seo settings Form -->
              <form method="POST" action="{{ url('admin/seo-settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                  <label for="meta_title" class="col-md-4 col-lg-3 col-form-label">Meta Title</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="meta_title" class="form-control" id="ckeditor7" rows="5"><?=$setting->meta_title?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="meta_description" class="col-md-4 col-lg-3 col-form-label">Meta Description</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="meta_description" class="form-control" id="ckeditor8" rows="5"><?=$setting->meta_description?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="meta_keywords" class="col-md-4 col-lg-3 col-form-label">Meta Keywords</label>
                  <div class="col-md-8 col-lg-9">
                    <textarea type="text" name="meta_keywords" class="form-control" id="ckeditor9" rows="5"><?=$setting->meta_keywords?></textarea>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form><!-- End seo settings Form -->
            </div>
          </div><!-- End Bordered Tabs -->
        </div>
      </div>
    </div>
  </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>