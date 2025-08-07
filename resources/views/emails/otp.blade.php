<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>OTP Mavoka</title>
</head>

<body style="font-family: Arial, sans-serif;">
    <div style="max-width: 500px; margin: auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; text-align: center;">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://res.cloudinary.com/dpdp49etr/image/upload/v1754539025/logo-fit-academy_pgbiyq.png" alt="Logo Fitacademy" style="height: 60px; margin-right: 8px;">
            <img src="https://res.cloudinary.com/dpdp49etr/image/upload/v1754539027/logo-mavoka_t5utj7.png" alt="Logo Mavoka" style="height: 60px;">
        </div>


        <p>Hi {{ $nama }},</p>

        <p>Kode OTP untuk akun mavoka Anda adalah</p>

        <div style="display: inline-block; min-width: 120px; text-align: center; font-size: 20px; font-weight: 600; background-color: #0F67B1; color: white; padding: 8px 16px; border-radius: 4px;">
            {{ $otp }}
        </div>


        <p style="margin-top: 20px;">
            Kode ini hanya berlaku satu kali selama <strong>10 menit</strong> dan hanya dapat digunakan untuk satu kali proses verifikasi.
            Harap jangan membagikan kode ini kepada siapapun.
        </p>
        <p>Terima Kasih</p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
        <p style="color: #9ca3af; font-size: 12px;">
            Email ini dikirim secara otomatis oleh sistem Mavoka. Jika Anda tidak merasa melakukan permintaan verifikasi ini, segera hubungi 6767–09765.
        </p>
        </p>
    </div>
</body>

</html>