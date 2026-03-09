<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALFA Member Card</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Segoe UI, sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 380px;
            aspect-ratio: 1.586;
            background: #0e0e0e;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            color: white;
            /* box-shadow: 0 20px 50px rgba(0, 0, 0, .6); */
        }
        .pattern {
            position: absolute;
            inset: 0;
            background-color: rgb(30, 30, 30);
            background-image: linear-gradient(#1a1a1a 3px, transparent 3px), linear-gradient(90deg, #1a1a1a 3px, transparent 3px);
            background-size: 29px 31px;
            mask-image: radial-gradient(circle at center, black 60%, transparent 100%);
            /* opacity: .7; */
        }
        .header {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
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
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 20px;
            margin-top: 8px;
            z-index: 2;
        }
        .photo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #f7c948;
            object-fit: cover;
        }
        .info {
            flex: 1;
        }
        .name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #f7c948;
            text-transform: capitalize;
            margin-bottom: 5px;
        }
        .phone {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
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
            bottom: 0;
            width: 100%;
            background: #111;
            padding: 10px 14px;
            text-align: center;
            font-size: 11px;
            line-height: 1.4;
            z-index: 2;
            border-top: 1px solid #333;
        }
        .footer strong {
            /* display: block; */
            color: #f7c948;
            font-size: 12px;
            margin-bottom: 2px;
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
            <img src="bg.jpg" class="photo">
            <div class="info">
                <div class="name"><?= (($member)?$member->name:'') ?></div>
                <div class="phone"><img src="<?=env('FRONT_ASSETS_URL')?>phone-call.png" alt=""> <?= (($member)?$member->phone:'') ?></div>
                <div class="address"><img src="<?=env('FRONT_ASSETS_URL')?>gps.png" alt=""> <?= (($member)?$member->address:'') ?></div>
            </div>
        </div>
        <div class="footer">
            <strong><?= $generalSetting->site_name ?></strong><br />
            <strong>Email:</strong> <a href="mailto:<?= $generalSetting->site_mail ?>" style="color: #fff; text-decoration: none;"><?= $generalSetting->site_mail ?></a> | <strong>Website:</strong> <a href="<?= $generalSetting->site_url ?>" target="_blank" style="color: #fff; text-decoration: none;"><?= $generalSetting->site_url ?></a>
        </div>
    </div>
</body>
</html>