<?php

use App\Models\GeneralSetting;

$generalSetting = GeneralSetting::find('1');
?>
<!doctype html>
<html lang="en">

<head>
  <title><?= $generalSetting->site_name ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:40px 15px;">
    <tr>
      <td align="center">

        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 0 20px rgba(0,0,0,0.08);">

          <tr>
            <td style="padding:30px;text-align:center;border-bottom:1px solid #eee;">
              <img src="<?= env('UPLOADS_URL') . $generalSetting->site_logo ?>"
                alt="<?= $generalSetting->site_name ?>"
                style="max-width:200px;width:100%;">
            </td>
          </tr>

          <tr>
            <td style="padding:30px;text-align:center;">

              <h2 style="margin:0 0 10px;color:#333;font-weight:600;">
                Welcome to <?= $generalSetting->site_name ?>!
              </h2>

              <p style="margin:0 0 25px;color:#666;font-size:15px;">
                Use the following One-Time Password (OTP) to proceed with your verification.
              </p>

              <table align="center" cellpadding="0" cellspacing="0">
                <tr>

                  <td style="padding:12px 16px;border:2px solid #FCC312;border-radius:6px;font-size:20px;font-weight:bold;color:#333;margin:5px;">
                    <?= substr($otp, 0, 1) ?>
                  </td>

                  <td width="10"></td>

                  <td style="padding:12px 16px;border:2px solid #FCC312;border-radius:6px;font-size:20px;font-weight:bold;color:#333;">
                    <?= substr($otp, 1, 1) ?>
                  </td>

                  <td width="10"></td>

                  <td style="padding:12px 16px;border:2px solid #FCC312;border-radius:6px;font-size:20px;font-weight:bold;color:#333;">
                    <?= substr($otp, 2, 1) ?>
                  </td>

                  <td width="10"></td>

                  <td style="padding:12px 16px;border:2px solid #FCC312;border-radius:6px;font-size:20px;font-weight:bold;color:#333;">
                    <?= substr($otp, 3, 1) ?>
                  </td>

                </tr>
              </table>

              <p style="margin-top:25px;font-size:14px;color:#888;">
                This OTP is valid for a limited time. Please do not share it with anyone.
              </p>

            </td>
          </tr>

          <tr>
            <td style="padding:20px;text-align:center;border-top:1px solid #eee;font-size:13px;color:#999;">
              © <?= date('Y') ?> <?= $generalSetting->site_name ?>. All rights reserved.
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>

</html>