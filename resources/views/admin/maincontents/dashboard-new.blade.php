<style>
   a {
      color: #717171;
      text-decoration: none;
   }
</style>
<div class="pagetitle">
   <h1><?= $page_header ?></h1>
   <nav>
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
         <li class="breadcrumb-item active"><?= $page_header ?></li>
      </ol>
   </nav>
</div>
<!-- End Page Title -->
<section class="section dashboard">
   <div class="row align-items-center">
      <div class="col-lg-4">
         <div class="card mb-1">
            <a href="<?= url('admin/committee-member/list') ?>">
               <div class="card-body">
                  <h6>Committee Members</h6>
                  <b><?= $committee_member_count ?></b>
               </div>
            </a>
         </div>
      </div>
      <div class="col-lg-4">
         <div class="card mb-1">
            <a href="<?= url('admin/member/list') ?>">
               <div class="card-body">
                  <h6>Members</h6>
                  <b><?= $normal_member_count ?></b>
               </div>
            </a>
         </div>
      </div>
      <div class="col-lg-4">
         <div class="card mb-1">
            <a href="<?= url('admin/core/list') ?>">
               <div class="card-body">
                  <h6>Cores</h6>
                  <b><?= $core_count ?></b>
               </div>
            </a>
         </div>
      </div>
      <div class="col-lg-4">
         <div class="card mb-1">
            <a href="<?= url('admin/core/list') ?>">
               <div class="card-body">
                  <h6>Core Members</h6>
                  <b><?= $core_member_count ?></b>
               </div>
            </a>
         </div>
      </div>
      <div class="col-lg-4">
         <div class="card mb-1">
            <a href="<?= url('admin/event/list') ?>">
               <div class="card-body">
                  <h6>Events</h6>
                  <b><?= $event_count ?></b>
               </div>
            </a>
         </div>
      </div>
      <div class="col-lg-4">
         <div class="card mb-1">
            <a href="<?= url('admin/privileges/list') ?>">
               <div class="card-body">
                  <h6>Privileges</h6>
                  <b><?= $privilege_count ?></b>
               </div>
            </a>
         </div>
      </div>
   </div>

   <h5 class="mt-3">Core Leader Board</h5>
   <div class="row align-items-center">
      <?php if ($cores) {
         foreach ($cores as $core) { ?>
            <div class="col-lg-4">
               <div class="card mb-1">
                  <a href="<?= url('admin/core/list') ?>">
                     <div class="card-body">
                        <h6><?= $core->name ?></h6>
                        <b><?= $core->points ?></b>
                     </div>
                  </a>
               </div>
            </div>
      <?php }
      } ?>
   </div>
</section>