<div class="pagetitle">
  <h1><?=$page_header?></h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?=url('admin/dashboard')?>">Home</a></li>
      <li class="breadcrumb-item active"><?=$page_header?></li>
    </ol>
  </nav>
</div><!-- End Page Title -->
<section class="section dashboard">
  <div class="row">
    <div class="col-lg-4 mb-3">
      <a href="<?=url('admin/dashboard-new')?>" class="btn btn-warning btn-sm">Dashboard New</a>
    </div>
    <div class="col-lg-4 mb-3">
      <a href="<?=url('admin/stats')?>" class="btn btn-warning btn-sm">Stats</a>
    </div>
    <div class="col-lg-4 mb-3">
      <a href="<?=url('admin/message')?>" class="btn btn-warning btn-sm">Message</a>
    </div>    
    <div class="col-lg-4 mb-3">
      <form method="GET" name="PostName" action="<?=url('admin/dashboard-filter')?>">
        @csrf
        <input type="hidden" name="mode" value="filter">
        <div class="row" style="border:1px solid #f9bb23; padding: 15px; border-radius: 10px;">
          <div class="col-lg-6 col-md-6 col-sm-6">
            <select class="form-control my-4" name="filter_keyword" onchange="PostName.submit()" style="width: 100%;">
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
          </div>
        </div>
      </form>
    </div>
    <!-- Left side columns -->
    <div class="col-lg-12">
      <div class="row">
        <!-- Sales Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total Products <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fab fa-product-hunt"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Sales Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total New Products <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fab fa-product-hunt"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total Active Products <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fab fa-product-hunt"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->


        
        
        <!-- Revenue Card -->
        <div class="col-xxl-3 col-md-3">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total Orders <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-3 col-md-3">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total New Orders <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-3 col-md-3">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total Rejected Orders <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-3 col-md-3">
          <div class="card info-card revenue-card">
            <div class="card-body">
              <h5 class="card-title">Total Cancelled Orders <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->


        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Total Customers <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="fas fa-users"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Total Sales <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->
        <!-- Revenue Card -->
        <div class="col-xxl-4 col-md-4">
          <div class="card info-card sales-card">
            <div class="card-body">
              <h5 class="card-title">Total Refunds <span>| <?=$filter_keyword_text?></span></h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="ps-3">
                  <h6></h6>
                  <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
                </div>
              </div>
            </div>
          </div>
        </div><!-- End Revenue Card -->

      </div>
    </div><!-- End Left side columns -->
  </div>
</section>