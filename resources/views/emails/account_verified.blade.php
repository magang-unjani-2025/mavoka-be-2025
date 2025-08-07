<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Verifikasi Berhasil</title>
  </head>
  <body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 40px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 32px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);">
      <h2 style="color: #111827;">Halo {{ $nama }},</h2>
      <p style="font-size: 16px; color: #374151;">
        Akun Anda telah <strong>berhasil diverifikasi</strong> oleh <span style="color: #3b82f6; font-weight: bold;">Mavoka</span>.
      </p>
      <p style="font-size: 16px; color: #374151;">
        Silakan login ke platform kami untuk mulai menggunakan fitur yang tersedia.
      </p>
      <div style="margin: 24px 0;">
        <a href="http://localhost:3000/login" target="_blank" style="background-color: #3b82f6; color: white; text-decoration: none; padding: 12px 20px; border-radius: 6px; display: inline-block;">
          Login Sekarang
        </a>
      </div>
      <p style="font-size: 16px; color: #374151;">Terima kasih 🙌</p>
    </div>
    <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
      &copy; {{ date('Y') }} Mavoka. All rights reserved.
    </p>
  </body>
</html>
