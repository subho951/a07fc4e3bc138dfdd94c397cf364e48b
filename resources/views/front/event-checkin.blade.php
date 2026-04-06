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
            min-height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 700px;
            background: white;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
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
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.35);
        }
        .content {
            padding: 40px 30px;
        }
        .title {
            font-size: 32px;
            color: #444;
            font-weight: 700;
            margin-bottom: 25px;
        }
        .profile {
            position: relative;
            width: 170px;
            margin: auto;
        }
        .profile img {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            border: 6px solid #fec514;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
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
        .name {
            margin-top: 18px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #fec514;
        }
        .username {
            font-size: 13px;
            color: #777;
            margin-top: 3px;
        }
        .status-box {
            max-width: 460px;
            margin: 20px auto 0;
            padding: 16px 18px;
            border-radius: 16px;
            font-weight: 600;
            line-height: 1.5;
        }
        .status-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .status-pending {
            color: #8a6500;
            background: #fff7d6;
            border: 1px solid #f1d05a;
        }
        .status-success {
            color: #165c34;
            background: #e8f7ec;
            border: 1px solid #7acb90;
        }
        .status-error {
            color: #8b1d1d;
            background: #fde8e8;
            border: 1px solid #f2a0a0;
        }
        .spinner {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid rgba(138, 101, 0, 0.25);
            border-top-color: #8a6500;
            animation: spin 0.9s linear infinite;
            flex: 0 0 auto;
        }
        .retry-btn {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 16px;
            border: none;
            border-radius: 999px;
            background: #444;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }
        .retry-btn:hover {
            opacity: 0.92;
        }
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
        .divider {
            margin: 30px auto;
            width: 80%;
            height: 1px;
            background: #ddd;
        }
        .footer {
            font-size: 14px;
            color: #777;
            line-height: 1.7;
        }
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        @media(max-width:600px) {
            .banner {
                height: 550px;
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
    <?php
        $isPending = !empty($checkin_pending);
        $isError = !empty($checkin_error);
    ?>
    <div class="card">
        <div class="banner" style="background: url('<?=(($event)?env('UPLOADS_URL').'event/'.$event->photo:env('NO_IMAGE'))?>') center/cover no-repeat;">
            <h1><?= (($event)?$event->title:'') ?></h1>
        </div>
        <div class="content">
            <div class="title"><?= ($isError ? 'Check-in Failed' : ($isPending ? 'Checking In...' : 'Congratulation !!!')) ?></div>
            <div class="profile">
                <img src="<?=(($member)?env('UPLOADS_URL').'user/'.$member->photo:env('NO_IMAGE'))?>">
                <div class="verify">&#10003;</div>
            </div>
            <div class="name"><?= (($member)?$member->name:'') ?></div>
            <div class="username">USERNAME : <?= (($member)?$member->phone:'') ?></div>
            <div class="status-box <?= ($isError ? 'status-error' : ($isPending ? 'status-pending' : 'status-success')) ?>" id="status-message">
                <div class="status-row">
                    <?php if($isPending && !$isError){ ?>
                        <div class="spinner"></div>
                    <?php } ?>
                    <div><?= e($checkin_msg ?? '') ?></div>
                </div>
                <?php if($isError){ ?>
                    <button type="button" class="retry-btn" onclick="window.location.reload();">Try Again</button>
                <?php } ?>
            </div>
            <div class="message">
                <?php if($isError){ ?>
                    We could not complete the check-in because location access was not available.
                <?php } elseif($isPending){ ?>
                    Please allow location access so we can capture your check-in coordinates.
                <?php } else { ?>
                    Your entry to the event <br>
                    <b><?= (($event)?$event->title:'') ?></b> <br>
                    has been confirmed
                <?php } ?>
            </div>
            <div class="divider"></div>
            <div class="footer">
                <?php if(!empty($user_event) && !empty($user_event->entry_timestamp)){ ?>
                    Present on : <?= e(date_format(date_create($user_event->entry_timestamp), "d.m.Y h:i A")) ?> <br>
                <?php } ?>
                At <?= e(($event)?$event->venue:'') ?>
                <?php if(!empty($user_event) && !empty($user_event->location)){ ?>
                    <br>Location: <?= e($user_event->location) ?>
                    <br>Latitude: <?= e($user_event->latitude) ?>, Longitude: <?= e($user_event->longitude) ?>
                <?php } ?>
            </div>
        </div>
    </div>
    <form id="checkin-location-form" method="POST" action="<?= e(request()->url()) ?>" style="display:none;">
        @csrf
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var shouldRequestLocation = <?= json_encode($isPending && !$isError) ?>;
            if (!shouldRequestLocation) {
                return;
            }

            var statusMessage = document.getElementById('status-message');
            var form = document.getElementById('checkin-location-form');
            var latitudeInput = document.getElementById('latitude');
            var longitudeInput = document.getElementById('longitude');

            var setStatus = function (message, isError) {
                if (!statusMessage) {
                    return;
                }

                statusMessage.classList.remove('status-pending', 'status-success', 'status-error');
                statusMessage.classList.add(isError ? 'status-error' : 'status-pending');

                var spinner = isError ? '' : '<div class="spinner"></div>';
                var retryButton = isError ? '<button type="button" class="retry-btn" onclick="window.location.reload();">Try Again</button>' : '';
                statusMessage.innerHTML = '<div class="status-row">' + spinner + '<div>' + message + '</div></div>' + retryButton;
            };

            if (typeof window.isSecureContext !== 'undefined' && !window.isSecureContext) {
                setStatus('Location access requires a secure connection (HTTPS or localhost).', true);
                return;
            }

            if (!navigator.geolocation) {
                setStatus('Your browser does not support location access.', true);
                return;
            }

            setStatus('Fetching your current location. Please allow access if prompted...', false);

            navigator.geolocation.getCurrentPosition(function (position) {
                latitudeInput.value = position.coords.latitude;
                longitudeInput.value = position.coords.longitude;
                form.submit();
            }, function (error) {
                var message = 'Unable to access your location.';

                if (error && error.code === 1) {
                    message = 'Location permission was denied. Please allow location access and try again.';
                } else if (error && error.code === 2) {
                    message = 'Location information is unavailable right now.';
                } else if (error && error.code === 3) {
                    message = 'Location request timed out. Please try again.';
                }

                setStatus(message, true);
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        });
    </script>
</body>
</html>
