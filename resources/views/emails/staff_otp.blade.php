<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Verification OTP</title>
</head>
<body style="margin:0;padding:0;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:#0f172a;color:#e2e8f0;">
    <table align="center" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#111827;border-radius:18px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.15);">
        <tr>
            <td style="padding:28px 32px;background:linear-gradient(135deg,#06b6d4 0%,#7c3aed 100%);color:#fff;">
                <h1 style="margin:0;font-size:26px;letter-spacing:-.02em;">Staff Account Verification</h1>
                <p style="margin:8px 0 0;font-size:15px;opacity:.85;">Please enter the OTP below within {{ $expiresIn }} seconds to verify the staff email.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:32px;">
                <p style="margin:0 0 12px;font-size:15px;color:#cbd5e1;">Hi Admin,</p>
                <p style="margin:0 0 16px;font-size:15px;color:#cbd5e1;">Your staff onboarding OTP is:</p>
                <div style="display:inline-block;padding:20px 28px;background:#0f172a;border:1px solid rgba(148,163,184,.15);border-radius:16px;font-size:28px;font-weight:700;letter-spacing:4px;color:#22d3ee;">{{ $otp }}</div>
                <p style="margin:24px 0 0;font-size:14px;line-height:1.6;color:#94a3b8;">Use this code on the staff creation page. It expires automatically after {{ $expiresIn }} seconds.</p>
                <p style="margin:24px 0 0;font-size:14px;line-height:1.6;color:#94a3b8;">If you did not request this, you can safely ignore this email.</p>
            </td>
        </tr>
        <tr>
            <td style="padding:20px 32px 32px;text-align:center;font-size:13px;color:#94a3b8;background:#0b1120;">
                <p style="margin:0;">Powered by Laravel POS</p>
            </td>
        </tr>
    </table>
</body>
</html>
