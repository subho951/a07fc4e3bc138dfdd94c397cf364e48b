<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Confirmation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            /* align-items: center; */
            min-height: 100vh;
        }
        /* Main Card */
        .card {
            width: 100%;
            max-width: 700px;
            background: white;
            /* border-radius: 14px; */
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        /* Banner */
        .banner {
            width: 100%;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .banner h1 {
            color: #fff;
            font-size: 42px;
            letter-spacing: 2px;
            font-weight: 700;
        }
        /* Content */
        .content {
            padding: 40px 30px;
        }
        /* Congrats */
        .title {
            font-size: 32px;
            color: #444;
            font-weight: 700;
            margin-bottom: 25px;
        }
        /* Profile */
        .profile {
            position: relative;
            width: 170px;
            margin: auto;
        }
        .profile img {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid #fec514;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        /* Check Badge */
        .verify {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: #5dc84a;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.2);
        }
        /* Name */
        .name {
            margin-top: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fec514
        }
        .username {
            font-size: 13px;
            color: #777;
            margin-top: 3px;
        }
        /* Message */
        .message {
            font-size: 15px;
            line-height: 1.6;
            color: #555;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.5), inset 0 -1px 0 rgba(255, 255, 255, 0.1), inset 0 0 20px 10px rgba(255, 255, 255, 1);
            position: relative;
            overflow: hidden;
            padding: 20px;
            max-width: 400px;
            margin: 20px auto;
        }
        /* Divider */
        .divider {
            margin: 30px auto;
            width: 80%;
            height: 1px;
            background: #ddd;
        }
        /* Footer */
        .footer {
            font-size: 14px;
            color: #777;
            line-height: 1.7;
        }
        /* Responsive */
        @media(max-width:600px) {
            .banner {
                height: 192px;
            }
            .banner h1 {
                font-size: 28px;
            }
            .title {
                font-size: 24px;
            }
            .profile img {
                width: 140px;
                height: 140px;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="banner" style="background: url(<?=(($event)?env('UPLOADS_URL').'event/'.$event->photo:env('NO_IMAGE'))?>) center/cover no-repeat;">
            <h1><?= (($event)?$event->title:'') ?></h1>
        </div>
        <div class="content">
            <div class="title">Congratulation !!!</div>
            <div class="profile">
                <img src="<?=(($member)?env('UPLOADS_URL').'user/'.$member->photo:env('NO_IMAGE'))?>">
                <div class="verify">✓</div>
            </div>
            <div class="name"><?= (($member)?$member->name:'') ?></div>
            <div class="username">USERNAME : <?= (($member)?$member->phone:'') ?></div>
            <div class="message">
                Your entry to the event <br>
                <b><?= (($event)?$event->title:'') ?></b> <br>
                has been confirmed
            </div>
            <div class="divider"></div>
            <div class="footer">
                Present on : <?= (($user_event)?date_format(date_create($user_event->entry_timestamp), "d.m.Y h:i A"):'') ?> <br>
                At <?= (($event)?$event->venue:'') ?>
            </div>
        </div>
    </div>
</body>
</html>