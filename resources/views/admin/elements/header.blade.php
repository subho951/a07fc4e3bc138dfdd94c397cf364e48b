<?php
use Illuminate\Http\Request;
?>
<div class="d-flex align-items-center justify-content-between">
  <a href="<?=url('admin/dashboard')?>" class="logo d-flex align-items-center">
    <!-- <img src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?=$generalSetting->site_name?>"> -->
    <span class="d-none d-lg-block" style="margin: 0 auto; color: #ffffff;"><?=$generalSetting->site_name?></span>
  </a>
  <i class="bi bi-list toggle-sidebar-btn"></i>
</div><!-- End Logo -->
<div class="search-bar">
  <!-- <form class="search-form d-flex align-items-center" method="POST" action="#">
    <input type="text" name="query" placeholder="Search" title="Enter search keyword">
    <button type="submit" title="Search"><i class="bi bi-search"></i></button>
  </form> -->
</div><!-- End Search Bar -->
<nav class="header-nav ms-auto">
  <ul class="d-flex align-items-center">
    <li class="nav-item d-block d-lg-none">
      <a class="nav-link nav-icon search-bar-toggle " href="#">
        <i class="bi bi-search"></i>
      </a>
    </li><!-- End Search Icon-->
    <div>
      <a href="<?=url('/')?>" target="_blank" class="btn btn-sm" style="margin-right:10px; background-color: #717171; color: #FFF; font-weight: 600;"><i class="fa fa-globe"></i>  Visit Website</a>
    </div>
    <li class="nav-item dropdown pe-3">
      <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
        <!-- <img src="<?=env('ADMIN_ASSETS_URL')?>assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
        <?php if($admin->image != ''){?>
          <img src="<?=env('UPLOADS_URL').$admin->image?>" alt="<?=$admin->name?>" class="rounded-circle">
        <?php } else {?>
          <img src="<?=env('NO_IMAGE')?>" alt="<?=$admin->name?>" class="img-thumbnail" class="rounded-circle">
        <?php }?>
        <span class="d-none d-md-block dropdown-toggle ps-2"><?=session('name')?></span>
      </a><!-- End Profile Iamge Icon -->
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
        <li class="dropdown-header">
          <h6><?=session('name')?></h6>
          <!-- <span>Web Designer</span> -->
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <!-- <li>
          <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
            <i class="bi bi-person"></i>
            <span>My Profile</span>
          </a>
        </li> -->
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item d-flex align-items-center" href="{{ url('admin/settings') }}">
            <i class="bi bi-gear"></i>
            <span>Account Settings</span>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <!-- <li>
          <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
            <i class="bi bi-question-circle"></i>
            <span>Need Help?</span>
          </a>
        </li> -->
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item d-flex align-items-center" href="{{ url('admin/logout') }}">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
          </a>
        </li>
      </ul><!-- End Profile Dropdown Items -->
    </li><!-- End Profile Nav -->
  </ul>
</nav><!-- End Icons Navigation -->