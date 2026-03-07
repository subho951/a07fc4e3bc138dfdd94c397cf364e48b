<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required Meta Tags Always Come First -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Favicons -->
  <link href="<?=env('UPLOADS_URL').$generalSetting->site_favicon?>" rel="icon">
  <!-- Title -->
  <title><?=$title?></title>
  <!-- Favicon -->
  <link rel="shortcut icon" href="favicon.ico">
  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&amp;display=swap" rel="stylesheet">
  <!-- CSS Implementing Plugins -->
  <link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>assets/css/vendor.min.css">
  <!-- CSS Front Template -->
  <link rel="stylesheet" href="<?=env('FRONT_THEME_ASSETS_URL')?>assets/css/style.css">
  <link rel="stylesheet" href="<?=env('ADMIN_ASSETS_URL')?>assets/css/theme.minc619.css?v=1.0">
  <link rel="preload" href="<?=env('ADMIN_ASSETS_URL')?>assets/css/theme.min.css" data-hs-appearance="default" as="style">
  <link rel="preload" href="<?=env('ADMIN_ASSETS_URL')?>assets/css/theme-dark.min.css" data-hs-appearance="dark" as="style">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />  
</head>
<body>
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/js/hs.theme-appearance.js"></script>
  <main id="content" role="main" class="main">
   <div class="container py-5 py-sm-7">
  <section id="about" class="privacy_policy">
      <div class="container" data-tm-padding-bottom="220px">
         <a class="d-flex justify-content-center mb-5" href="javascript:void(0);">
           <img class="zi-2" src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="Image Description" style="width: 8rem;">
         </a>
         <h4><?=(($page)?$page->page_title:'')?></h4>
         <div class="section-content">
            <div class="row">
               <div class="col-lg-12 col-xl-12 wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.1s">
                  <div class="about-text-content mb-md-30">
                     <?=(($page)?$page->long_description:'')?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>
</div>
</main>
  <!-- JS Implementing Plugins -->
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/js/vendor.min.js"></script>
  <!-- JS Front -->
  <script src="<?=env('ADMIN_ASSETS_URL')?>assets/js/theme.min.js"></script>
  <!-- JS Plugins Init. -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
</html>