@php
    $siteName = $site_name ?? ((isset($generalSetting) && $generalSetting && $generalSetting->site_name != '') ? $generalSetting->site_name : 'ALFA Network');
    $themeColor = $theme_color ?? ((isset($generalSetting) && $generalSetting && $generalSetting->theme_color != '') ? $generalSetting->theme_color : '#FCC312');
    $fontColor = $font_color ?? ((isset($generalSetting) && $generalSetting && $generalSetting->font_color != '') ? $generalSetting->font_color : '#1F2937');
    $fontFamily = $font_family ?? "'Segoe UI', 'Helvetica Neue', Arial, sans-serif";
    $logo = ((isset($generalSetting) && $generalSetting && $generalSetting->site_logo != '') ? env('UPLOADS_URL').$generalSetting->site_logo : env('NO_IMAGE'));
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteName }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f8;font-family:{!! $fontFamily !!};color:{{ $fontColor }};">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 15px;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 10px 35px rgba(0,0,0,0.10);">
                    <tr>
                        <td style="background:{{ $themeColor }};padding:18px 24px;text-align:center;">
                            <img src="{{ $logo }}" alt="{{ $siteName }}" style="max-width:210px;width:100%;height:auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 34px 22px;text-align:center;">
                            <h1 style="margin:0 0 12px;font-size:30px;line-height:1.2;font-weight:700;color:{{ $fontColor }};">Happy Birthday, {{ $name }}!</h1>
                            <p style="margin:0 0 24px;font-size:16px;line-height:1.7;color:#4b5563;">
                                {{ $wish_message ?? 'Wishing you a wonderful birthday and an amazing year ahead.' }}
                            </p>
                            <table align="center" cellpadding="0" cellspacing="0" style="margin:0 auto 12px;">
                                <tr>
                                    <td style="background:{{ $themeColor }};border-radius:999px;padding:12px 30px;color:#ffffff;font-size:14px;font-weight:600;letter-spacing:0.2px;">
                                        Warm Wishes From {{ $siteName }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 34px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:10px;background:#fafafa;">
                                <tr>
                                    <td style="padding:16px 18px;font-size:14px;line-height:1.7;color:#4b5563;text-align:center;">
                                        Thank you for being a valuable part of our community. We are excited to celebrate your journey and achievements with you.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #e5e7eb;padding:16px 24px;text-align:center;background:#fcfcfc;font-size:12px;color:#6b7280;">
                            © {{ date('Y') }} {{ $siteName }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
