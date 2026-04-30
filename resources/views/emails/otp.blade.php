<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        
        <h2 style="color: #007bff; text-align: center;">
            @if($lang == 'en')
                [{{ $companyName }} Employee Self-Data Update] - Account Verification
            @else
                [Pembaruan Data Mandiri Karyawan {{ $companyName }}] - Verifikasi Akun
            @endif
        </h2>
        
        <p style="text-align: center;">
            {{ $lang == 'en' ? 'Your Password Activation OTP Code:' : 'Kode OTP Aktivasi Password Anda:' }}
        </p>

        <div style="background: #f4f4f4; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; border-radius: 5px; margin: 20px 0;">
            {{ $otp }}
        </div>

        <p style="font-size: 12px; color: #888; margin-top: 20px;">
            @if($lang == 'en')
                *This code is valid for <strong>5 minutes</strong>. <br>
                *For security, do not share this code with anyone, including HR staff.
            @else
                *Kode ini berlaku selama <strong>5 menit</strong>. <br>
                *Demi keamanan, jangan berikan kode ini kepada siapapun termasuk pihak HR.
            @endif
        </p>

        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

        <p style="font-size: 11px; color: #aaa; text-align: center;">
            @if($lang == 'en')
                This email was sent automatically by the {{ $companyName }} Employee Database System.<br>
                Please do not reply to this email.
            @else
                Email ini dikirim secara otomatis oleh Sistem Database Karyawan {{ $companyName }}.<br>
                Mohon tidak membalas email ini.
            @endif
        </p>
    </div>
</body>
</html>