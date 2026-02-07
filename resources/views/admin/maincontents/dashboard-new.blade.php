<div class="pagetitle">
   <h1><?=$page_header?></h1>
   <nav>
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
         <li class="breadcrumb-item active"><?=$page_header?></li>
      </ol>
   </nav>
</div>
<!-- End Page Title -->
<section class="section dashboard">
   <h3 class="dashboard-title">
      Stats overview for 
      <!-- <select class="form-control1">
         <option>Last 7 days</option>
      </select> -->
      <form method="GET" name="PostName" action="<?=url('admin/dashboard-filter')?>">
        @csrf
        <input type="hidden" name="mode" value="filter">
        <!-- <div class="row" style="border:1px solid #f9bb23; padding: 15px; border-radius: 10px;">
          <div class="col-lg-6 col-md-6 col-sm-6"> -->
            <select class="form-control1 my-3" name="filter_keyword" onchange="PostName.submit()" style="width: 100%;">
              <option value="" <?=(($filter_keyword == '')?'selected':'')?>>All Time</option>
              <option value="today" <?=(($filter_keyword == 'today')?'selected':'')?>>Today</option>
              <option value="yesterday" <?=(($filter_keyword == 'yesterday')?'selected':'')?>>Yesterday</option>
              <option value="this_month" <?=(($filter_keyword == 'this_month')?'selected':'')?>>This Month</option>
              <option value="last_month" <?=(($filter_keyword == 'last_month')?'selected':'')?>>Last Month</option>
              <option value="last_7_days" <?=(($filter_keyword == 'last_7_days')?'selected':'')?>>Last 7 Days</option>
              <option value="last_30_days" <?=(($filter_keyword == 'last_30_days')?'selected':'')?>>Last 30 Days</option>
              <option value="this_year" <?=(($filter_keyword == 'this_year')?'selected':'')?>>This Year</option>
              <option value="last_year" <?=(($filter_keyword == 'last_year')?'selected':'')?>>Last Year</option>
            </select>
          <!-- </div>
        </div> -->
      </form>
      <a href="<?=url('admin/stats')?>">View Detailed Stats</a>
   </h3>
   <ul class="stats-overview-list">
      <li>
         <h6>Total Views</h6>
         <b><?=$total_view?></b>
         <!-- <span><i class="fa-solid fa-arrow-up"></i> 13% Yoy</span> -->
         <label><i class="fa-regular fa-clock"></i> Just Now</label>
      </li>
      <li>
         <h6>Total Visits</h6>
         <b><?=$total_visit?></b>
         <!-- <span><i class="fa-solid fa-arrow-up"></i> 13% Yoy</span> -->
         <label><i class="fa-regular fa-clock"></i> Just Now</label>
      </li>
      <li>
         <h6>Total Orders</h6>
         <b><?=$total_orders?></b>
         <!-- <span><i class="fa-solid fa-arrow-up"></i> 13% Yoy</span> -->
         <label><i class="fa-regular fa-clock"></i> Just Now</label>
      </li>
      <li>
         <h6>Total Revenue</h6>
         <b><?=number_format($total_sales,2)?></b>
         <!-- <span><i class="fa-solid fa-arrow-up"></i> 13% Yoy</span> -->
         <label><i class="fa-regular fa-clock"></i> Just Now</label>
      </li>
   </ul>
   <h3 class="dashboard-title">Your open orders</h3>
   <p>In order of urgency</p>
   <div class="row align-items-center">
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>New</h6>
               <b><?=$total_new_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>Processing</h6>
               <b><?=$total_processing_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>Incomeplete</h6>
               <b><?=$total_incomplete_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>Shipped</h6>
               <b><?=$total_shipped_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>Complete</h6>
               <b><?=$total_complete_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>Rejected</h6>
               <b><?=$total_rejected_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="card mb-1">
            <div class="card-body">
               <h6>Cancelled</h6>
               <b><?=$total_cancelled_orders?> Orders</b>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <a href="<?=url('admin/orders/list/MQ%3D%3D/MA%3D%3D')?>">All Orders <i class="fa-solid fa-arrow-right"></i></a>
      </div>
   </div>
   <div class="card listing-card">
      <div class="card-header">
         <b>Listings</b>
         <a href="<?=url('admin/product/list')?>">View all listing</a>
      </div>
      <div class="card-header">
         Active Listing
         <b><?=$total_active_products?></b>
      </div>
      <div class="card-header">
         Deactive Listing 
         <b><?=$total_deactive_products?></b>
      </div>
      <div class="card-header">
         Draft Listing
         <b><?=$total_draft_products?></b>
      </div>
   </div>
   <h3 class="dashboard-title">Recent Activity</h3>
   <?php if($recent_activities){ foreach($recent_activities as $recent_activity){?>
      <div class="table-item mb-3 recent_activity">
         <?php if($recent_activity->comment != ''){?>
            <img src="<?=env('UPLOADS_URL').'user/'.$recent_activity->profile_image?>" width="60" style="width: 60px; height: 60px; border-radius: 50%;">
         <?php } else {?>
            <img src="<?=env('NO_IMAGE')?>" width="60" style="width: 60px; height: 60px; border-radius: 50%;">
         <?php } ?>
         <div>
            <a href="<?=url('admin/user-all-activity')?>">
               <?=$recent_activity->comment?>
            </a>
            <p><i class="fa-regular fa-clock"></i> <?=date_format(date_create($recent_activity->created_at), "M d, Y h:i A")?></p>
         </div>
      </div>
   <?php } }?>
   <a href="<?=url('admin/user-all-activity')?>">All Recent Activity</a>
</section>