<!DOCTYPE html>
<html lang="en">
<head>
  <?php
    $siteName = (($generalSetting && $generalSetting->site_name != '') ? $generalSetting->site_name : 'ALFA Network');
    $logo = (($generalSetting && $generalSetting->site_logo != '') ? env('UPLOADS_URL') . $generalSetting->site_logo : env('NO_IMAGE'));
    $favicon = (($generalSetting && $generalSetting->site_favicon != '') ? env('UPLOADS_URL') . $generalSetting->site_favicon : env('NO_IMAGE'));
    $description = (($generalSetting && $generalSetting->description != '') ? trim(strip_tags($generalSetting->description)) : '');
    $description = (($description != '') ? $description : 'The premium networking platform for modern business professionals.');
    $androidLink = 'https://play.google.com/store/apps/details?id=com.alfa.network';
    $iosLink = 'https://apps.apple.com/in/app/alfa-network/id6760302082';
  ?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $siteName ?> | Mobile App</title>
  <meta name="description" content="<?= e($description) ?>">
  <link href="<?= $favicon ?>" rel="icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    :root {
      --brand: #fcc312;
      --brand-dark: #d89f00;
      --ink: #101114;
      --muted: #5a6172;
      --surface: #ffffff;
      --soft: #f5f7fb;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: "League Spartan", sans-serif;
      color: var(--ink);
      background:
        radial-gradient(circle at 5% -10%, rgba(252, 195, 18, 0.42), transparent 30rem),
        radial-gradient(circle at 98% 10%, rgba(15, 24, 42, 0.09), transparent 24rem),
        linear-gradient(180deg, #fffef9 0%, #ffffff 50%, #f7f9ff 100%);
      min-height: 100vh;
      line-height: 1.4;
    }
    .container {
      width: min(1140px, 92%);
      margin: 0 auto;
    }
    .topbar {
      padding: 26px 0 8px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: inherit;
    }
    .brand img {
      height: 52px;
      width: auto;
      object-fit: contain;
      filter: drop-shadow(0 5px 12px rgba(0, 0, 0, 0.16));
    }
    .brand span {
      font-size: clamp(24px, 3.1vw, 34px);
      font-weight: 700;
      letter-spacing: 0.02em;
    }
    .hero {
      display: grid;
      grid-template-columns: 1.06fr 0.94fr;
      gap: 34px;
      align-items: center;
      padding: 36px 0 42px;
    }
    .eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #fff5cd;
      color: #8f6b00;
      border: 1px solid #ffe48a;
      border-radius: 999px;
      font-weight: 600;
      font-size: 15px;
      padding: 10px 14px;
      margin-bottom: 18px;
    }
    h1 {
      font-size: clamp(40px, 6vw, 76px);
      font-weight: 800;
      line-height: 0.95;
      margin-bottom: 16px;
      letter-spacing: -0.02em;
    }
    h1 .highlight {
      color: var(--brand-dark);
    }
    .hero p {
      font-size: clamp(18px, 2.3vw, 22px);
      color: var(--muted);
      max-width: 620px;
      margin-bottom: 22px;
      line-height: 1.45;
    }
    .cta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-bottom: 18px;
    }
    .store-btn {
      min-width: 210px;
      border-radius: 14px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 15px 18px;
      font-size: 18px;
      font-weight: 700;
      transition: 0.25s ease;
      border: 1px solid transparent;
    }
    .store-btn.android {
      background: linear-gradient(135deg, #111217, #1e2230);
      color: #fff;
      box-shadow: 0 10px 24px rgba(16, 18, 23, 0.22);
    }
    .store-btn.ios {
      background: linear-gradient(135deg, #fcc312, #efb800);
      color: #19160a;
      box-shadow: 0 10px 24px rgba(252, 195, 18, 0.34);
    }
    .store-btn:hover {
      transform: translateY(-2px);
    }
    .meta-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    .meta-chip {
      border: 1px solid #ebedf3;
      background: #fff;
      color: #30384a;
      border-radius: 999px;
      font-size: 15px;
      font-weight: 600;
      padding: 9px 14px;
    }
    .showcase {
      position: relative;
      border-radius: 26px;
      background: linear-gradient(155deg, #11141d, #1f2637 55%, #2b3348);
      padding: 30px;
      min-height: 470px;
      color: #fff;
      box-shadow: 0 18px 50px rgba(12, 18, 34, 0.3);
      overflow: hidden;
      isolation: isolate;
    }
    .showcase::before {
      content: "";
      position: absolute;
      width: 270px;
      height: 270px;
      border-radius: 50%;
      right: -70px;
      top: -85px;
      background: radial-gradient(circle, rgba(252, 195, 18, 0.82), rgba(252, 195, 18, 0));
      z-index: -1;
    }
    .showcase::after {
      content: "";
      position: absolute;
      inset: 0;
      background:
        linear-gradient(120deg, rgba(255, 255, 255, 0.09), rgba(255, 255, 255, 0) 30%),
        repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.06) 0 1px, transparent 1px 46px);
      opacity: 0.55;
      pointer-events: none;
    }
    .showcase h2 {
      font-size: clamp(28px, 4.2vw, 42px);
      line-height: 1.02;
      margin-bottom: 12px;
      letter-spacing: -0.01em;
    }
    .showcase p {
      color: rgba(255, 255, 255, 0.85);
      font-size: 19px;
      margin-bottom: 20px;
    }
    .showcase ul {
      list-style: none;
      display: grid;
      gap: 10px;
    }
    .showcase li {
      background: rgba(255, 255, 255, 0.09);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 12px 14px;
      border-radius: 12px;
      font-size: 17px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 500;
    }
    .showcase li i {
      color: var(--brand);
    }
    .logo-badge {
      margin-top: 22px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: rgba(252, 195, 18, 0.14);
      border: 1px solid rgba(252, 195, 18, 0.4);
      border-radius: 999px;
      padding: 8px 14px;
    }
    .logo-badge img {
      height: 32px;
      width: auto;
      object-fit: contain;
      border-radius: 8px;
      background: #fff;
      padding: 3px 7px;
    }
    .section-title {
      text-align: center;
      font-size: clamp(30px, 4vw, 48px);
      font-weight: 700;
      letter-spacing: -0.01em;
      margin-bottom: 10px;
    }
    .section-subtitle {
      text-align: center;
      font-size: 20px;
      color: var(--muted);
      margin-bottom: 24px;
    }
    .feature-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
      margin-bottom: 42px;
    }
    .feature-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(4px);
      border: 1px solid #eceff4;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 10px 24px rgba(17, 24, 39, 0.06);
      transition: 0.2s ease;
    }
    .feature-card:hover {
      transform: translateY(-4px);
    }
    .feature-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      background: #fff4c2;
      color: #8f6b00;
      margin-bottom: 14px;
    }
    .feature-card h3 {
      font-size: 24px;
      margin-bottom: 8px;
      font-weight: 700;
    }
    .feature-card p {
      font-size: 18px;
      color: var(--muted);
      line-height: 1.45;
    }
    .download-box {
      background: linear-gradient(135deg, #ffffff, #fff8df);
      border: 1px solid #f3dc8f;
      border-radius: 20px;
      padding: 26px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 26px;
    }
    .download-box h4 {
      font-size: clamp(26px, 4vw, 40px);
      margin-bottom: 6px;
    }
    .download-box p {
      font-size: 19px;
      color: #525f77;
    }
    .download-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    footer {
      padding: 18px 0 34px;
      text-align: center;
      color: #6c7485;
      font-size: 18px;
    }
    .social-links {
      margin-top: 12px;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 8px;
    }
    .social-links a {
      width: 38px;
      height: 38px;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      border: 1px solid #e6e9f0;
      color: #4f596f;
      background: #fff;
      transition: 0.2s ease;
    }
    .social-links a:hover {
      transform: translateY(-2px);
      border-color: #f3d06a;
      color: #705200;
      background: #fff8df;
    }
    @media (max-width: 1060px) {
      .hero {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      .showcase {
        min-height: auto;
      }
      .feature-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
      .download-box {
        flex-direction: column;
        align-items: flex-start;
      }
    }
    @media (max-width: 680px) {
      .topbar {
        padding-top: 20px;
      }
      .brand img {
        height: 44px;
      }
      .brand span {
        font-size: 26px;
      }
      .feature-grid {
        grid-template-columns: 1fr;
      }
      .store-btn {
        width: 100%;
      }
      .download-actions {
        width: 100%;
      }
      .download-actions .store-btn {
        min-width: 100%;
      }
      .hero {
        padding-top: 16px;
      }
    }
  </style>
</head>
<body>
  <header class="container topbar">
    <a class="brand" href="<?= url('/') ?>">
      <img src="<?= $logo ?>" alt="<?= e($siteName) ?>">
      <span><?= e($siteName) ?></span>
    </a>
  </header>

  <main class="container">
    <section class="hero">
      <div>
        <div class="eyebrow">
          <i class="fa-solid fa-star"></i>
          Premium Mobile Experience
        </div>
        <h1>Grow your network with <span class="highlight">ALFA</span></h1>
        <p><?= e($description) ?></p>
        <div class="cta-row">
          <a class="store-btn android" href="<?= $androidLink ?>" target="_blank" rel="noopener">
            <i class="fa-brands fa-google-play"></i>
            Get It on Android
          </a>
          <a class="store-btn ios" href="<?= $iosLink ?>" target="_blank" rel="noopener">
            <i class="fa-brands fa-apple"></i>
            Download on iOS
          </a>
        </div>
        <div class="meta-row">
          <span class="meta-chip"><i class="fa-solid fa-shield-halved"></i> Secure Access</span>
          <span class="meta-chip"><i class="fa-solid fa-bolt"></i> Fast Performance</span>
          <span class="meta-chip"><i class="fa-solid fa-user-group"></i> Professional Community</span>
        </div>
      </div>
      <div class="showcase">
        <h2>One app. All your member connections.</h2>
        <p>Access events, profiles, and opportunities from a polished mobile experience.</p>
        <ul>
          <li><i class="fa-solid fa-check"></i> Discover and connect with members instantly</li>
          <li><i class="fa-solid fa-check"></i> Track activity and stay updated with events</li>
          <li><i class="fa-solid fa-check"></i> Built for modern professional networking</li>
        </ul>
        <div class="logo-badge">
          <img src="<?= $logo ?>" alt="<?= e($siteName) ?>">
          <span><?= e($siteName) ?> App</span>
        </div>
      </div>
    </section>

    <section>
      <h2 class="section-title">Why Members Choose <?= e($siteName) ?></h2>
      <p class="section-subtitle">Designed for premium networking with speed, elegance, and trust.</p>
      <div class="feature-grid">
        <article class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-network-wired"></i></div>
          <h3>Smart Networking</h3>
          <p>Connect with relevant people faster through a focused community platform.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-calendar-days"></i></div>
          <h3>Event First</h3>
          <p>Stay synced with upcoming activities, participation, and engagement in one place.</p>
        </article>
        <article class="feature-card">
          <div class="feature-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
          <h3>Seamless Mobile UX</h3>
          <p>A premium interface crafted for both Android and iOS with responsive performance.</p>
        </article>
      </div>
    </section>

    <section class="download-box">
      <div>
        <h4>Download the <?= e($siteName) ?> app today</h4>
        <p>Available now on Google Play and App Store.</p>
      </div>
      <div class="download-actions">
        <a class="store-btn android" href="<?= $androidLink ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-google-play"></i>
          Android
        </a>
        <a class="store-btn ios" href="<?= $iosLink ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-apple"></i>
          iOS
        </a>
      </div>
    </section>
  </main>

  <footer class="container">
    <div>&copy; <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved.</div>
    <div class="social-links">
      <?php if($generalSetting && $generalSetting->facebook_profile != ''){ ?>
        <a href="<?= $generalSetting->facebook_profile ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      <?php } ?>
      <?php if($generalSetting && $generalSetting->instagram_profile != ''){ ?>
        <a href="<?= $generalSetting->instagram_profile ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
      <?php } ?>
      <?php if($generalSetting && $generalSetting->linkedin_profile != ''){ ?>
        <a href="<?= $generalSetting->linkedin_profile ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
      <?php } ?>
      <?php if($generalSetting && $generalSetting->youtube_profile != ''){ ?>
        <a href="<?= $generalSetting->youtube_profile ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
      <?php } ?>
      <?php if($generalSetting && $generalSetting->twitter_profile != ''){ ?>
        <a href="<?= $generalSetting->twitter_profile ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
      <?php } ?>
    </div>
  </footer>
</body>
</html>
