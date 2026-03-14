<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>ALFA Network Member Card</title>
    <style>
        @font-face {
            font-family: 'League Spartan';
            src: url(LeagueSpartan-VariableFont_wght.ttf);
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Segoe UI, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            /* align-items: center; */
            height: 100%;
        }

        .card {
            width: 380px;
            height: 250px;
            /* aspect-ratio: 1.586; */
            background: #0e0e0e;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            color: white;
            margin: 40px 15px 0;
            /* box-shadow: 0 20px 50px rgba(0, 0, 0, .6); */
        }

        .pattern {
            position: absolute;
            inset: 0;
            background: url(<?=env('FRONT_ASSETS_URL')?>bg.jpeg) no-repeat center/cover;
        }

        .header {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px 0;
            z-index: 2;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            color: #f7c948;
        }

        .logo span {
            display: block;
            font-size: 12px;
            letter-spacing: 3px;
            color: #fff;
        }

        .profile {
            position: relative;
            padding: 0 20px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            top: -15px;
        }

        .photo {
            width: 70px;
            height: 70px;
            object-fit: cover;
        }

        /* Info */
        .info{
            text-align: center;
        }
        .info h2 {
            font-size: 20px;
            font-weight: 700;
        }

        .company {
            color: #edecec;
            font-size: 15px;
        }

        /* Contact */

        .contact {
            display: flex;
            justify-content: center;
            gap: 10px;
            font-size: 15px;
            color: #edecec;
            margin-top: 5px;
        }
        .contact span {
            display: flex;
            align-items: center;
        } 
        .contact i {
            margin-right: 5px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 50%;
            color: #afafaf;
            font-size: 14px;
        }


        .address {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: .8;
        }

        .footer {
            position: absolute;
            bottom: 2px;
            width: 100%;
            padding: 5px 10px;
            text-align: center;
            font-size: 11px;
            line-height: 1.4;
            z-index: 2;
        }

        .footer .title{
            display:inline-block;
            background:#fcc311;
            color:#fff;
            padding:3px 6px;
            font-weight:700;
            letter-spacing: 1px;
        }

        .footer p{
            color:#222;
            font-weight:600;
            letter-spacing:1px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="pattern"></div>
        <div class="header">
            <div class="logo">
                <img src="<?=env('UPLOADS_URL').$generalSetting->site_logo?>" alt="<?= $generalSetting->site_name ?>" style="width: 100px;">
            </div>
        </div>
        <div class="profile">
            <img src="<?=env('UPLOADS_URL').'user/'.(($member)?$member->photo:'')?>" class="photo">
            <div class="info">
                <h2><?= (($member)?$member->name:'') ?></h2>
                <p class="company"><?= (($member)?$member->company_name:'') ?></p>
                <div class="contact">
                    <span><i class="fa-solid fa-envelope"></i> <?= (($member)?$member->email:'') ?></span>
                    <span><i class="fa-solid fa-phone"></i> <?= (($member)?$member->phone:'') ?></span>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="title"><?= $generalSetting->site_name ?></div>
            <p><?= $generalSetting->site_mail ?> | <?= $generalSetting->site_url ?></p>
        </div>
    </div>
</body>
</html>