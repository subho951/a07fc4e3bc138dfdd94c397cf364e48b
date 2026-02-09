<?php
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Route;
$routeName    = Route::current();
$pageName     = explode("/", $routeName->uri());
$pageSegment  = $pageName[1];
$pageFunction = ((count($pageName)>2)?$pageName[1]:'');
$parameters   = $routeName->parameters();
// dd($routeName);
if(!empty($parameters)){
  if (array_key_exists("id1",$parameters)){
    $pId1 = Helper::decoded($parameters['id1']);
  } else {
    $pId1 = Helper::decoded($parameters['id']);
  }
  if(count($parameters) > 1){
    $pId2 = Helper::decoded($parameters['id2']);
  }
}
?>
<ul class="sidebar-nav" id="sidebar-nav">
  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'dashboard')?'active':'')?>" href="{{ url('admin/dashboard') }}">
      <i class="fa fa-home"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'committee-member')?'active':'')?>" href="{{ url('admin/committee-member/list') }}">
      <i class="fa fa-users"></i>
      <span>Committee Members</span>
    </a>
  </li><!-- End Teacher Members Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'event')?'active':'')?>" href="{{ url('admin/event/list') }}">
      <i class="fa fa-calendar"></i>
      <span>Events</span>
    </a>
  </li><!-- End Teacher Members Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'categories')?'active':'')?>" href="{{ url('admin/categories/list') }}">
      <i class="fa fa-list-alt"></i>
      <span>Privilege Categories</span>
    </a>
  </li><!-- End Privilege Categories Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'privileges')?'active':'')?>" href="{{ url('admin/privileges/list') }}">
      <i class="fa-solid fa-handshake"></i>
      <span>Privileges</span>
    </a>
  </li><!-- End Privileges Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'page')?'active':'')?>" href="{{ url('admin/page/list') }}">
      <i class="fa fa-file-text"></i>
      <span>Pages</span>
    </a>
  </li><!-- End Teacher Members Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'email-logs')?'active':'')?>" href="{{ url('admin/email-logs') }}">
      <i class="fa fa-envelope"></i>
      <span>Email Logs</span>
    </a>
  </li><!-- End Email Logs Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'login-logs')?'active':'')?>" href="{{ url('admin/login-logs') }}">
      <i class="fa fa-list"></i>
      <span>Login Logs</span>
    </a>
  </li><!-- End Login Logs Nav -->

  <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'settings')?'active':'')?>" href="{{ url('admin/settings') }}">
      <i class="fa fa-cogs"></i>
      <span>Account Settings</span>
    </a>
  </li><!-- End Account Settings Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'institute')?'active':'')?>" href="{{ url('admin/institute/list') }}">
      <i class="fa fa-university"></i>
      <span>Institutes</span>
    </a>
  </li>End Institutes Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'category')?'active':'')?>" href="{{ url('admin/category/list') }}">
      <i class="fa fa-list-alt"></i>
      <span>Categories</span>
    </a>
  </li>End Institutes Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'society-member')?'active':'')?>" href="{{ url('admin/society-member/list') }}">
      <i class="fa fa-users"></i>
      <span>Society Members</span>
    </a>
  </li>End Society Members Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'employee-member')?'active':'')?>" href="{{ url('admin/employee-member/list') }}">
      <i class="fa fa-users"></i>
      <span>Admin & Employee Members</span>
    </a>
  </li>End Society Members Nav -->  

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'news')?'active':'')?>" href="{{ url('admin/news/list') }}">
      <i class="fa-solid fa-magnifying-glass"></i>
      <span>News</span>
    </a>
  </li>End News Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'magazine')?'active':'')?>" href="{{ url('admin/magazine/list') }}">
      <i class="fa fa-newspaper"></i>
      <span>Magazines</span>
    </a>
  </li>End Magazines Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'achievement')?'active':'')?>" href="{{ url('admin/achievement/list') }}">
      <i class="fa fa-award"></i>
      <span>Achievements</span>
    </a>
  </li>End Achievements Nav -->

  <!-- <li class="nav-item">
    <a class="nav-link <?=(($pageSegment == 'media')?'active':'')?>" href="{{ url('admin/media/institute-list') }}">
      <i class="fa-solid fa-image"></i>
      <span>Media</span>
    </a>
  </li>End Achievements Nav -->

  
</ul>