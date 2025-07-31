<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mavoka - Verifikasi Kode OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; margin: 0; padding: 0;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 30px;">
                    <tr>
                        <td style="text-align: center;">
                            <h2 style="color: #111827; margin-bottom: 10px;">Verifikasi Akun Mavoka</h2>
                            <p style="color: #6b7280; font-size: 16px; margin: 0;">
                                Halo 👋<br>
                                Berikut adalah kode OTP untuk verifikasi akun kamu:
                            </p>
                            <div style="margin: 30px 0;">
                                <span style="display: inline-block; font-size: 36px; font-weight: bold; color: #4f46e5; letter-spacing: 4px;">
                                    {{ $otp }}
                                </span>
                            </div>
                            <p style="color: #6b7280; font-size: 14px; margin-bottom: 20px;">
                                Kode ini hanya berlaku selama <strong>10 menit</strong>. Mohon jangan membagikan kode ini kepada siapapun.
                            </p>
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                            <p style="color: #9ca3af; font-size: 12px;">
                                Email ini dikirim secara otomatis oleh sistem Mavoka. Jika kamu tidak merasa melakukan permintaan ini, abaikan saja email ini.
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="color: #9ca3af; font-size: 12px; margin-top: 20px;">
                    &copy; {{ date('Y') }} Mavoka. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
