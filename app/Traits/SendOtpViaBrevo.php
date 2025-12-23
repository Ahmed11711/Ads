<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait SendOtpViaBrevo
{
 public function sendOtp(string $email, string $otp)
 {
  $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>OTP Verification</title>
        </head>
        <body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" style="padding:40px 0;">
                        <table width="100%" max-width="500px" cellpadding="0" cellspacing="0"
                            style="background:#ffffff; border-radius:8px; padding:30px; box-shadow:0 4px 12px rgba(0,0,0,0.08);">

                            <!-- Header -->
                            <tr>
                                <td align="center" style="padding-bottom:20px;">
                                    <h1 style="margin:0; color:#1f2937;">Moto</h1>
                                    <p style="margin:5px 0 0; color:#6b7280; font-size:14px;">
                                        Secure Verification
                                    </p>
                                </td>
                            </tr>

                            <!-- Divider -->
                            <tr>
                                <td>
                                    <hr style="border:none; border-top:1px solid #e5e7eb; margin:20px 0;">
                                </td>
                            </tr>

                            <!-- Content -->
                            <tr>
                                <td style="color:#374151; font-size:15px; line-height:1.6;">
                                    <p>Hello 👋</p>
                                    <p>
                                        Use the following One-Time Password (OTP) to verify your email address.
                                        This code is valid for <strong> </strong>.
                                    </p>
                                </td>
                            </tr>

                            <!-- OTP Box -->
                            <tr>
                                <td align="center" style="padding:25px 0;">
                                    <div style="
                                        display:inline-block;
                                        padding:15px 30px;
                                        font-size:28px;
                                        letter-spacing:6px;
                                        font-weight:bold;
                                        color:#2563eb;
                                        background:#eff6ff;
                                        border-radius:8px;
                                        border:1px dashed #2563eb;
                                    ">
                                        ' . $otp . '
                                    </div>
                                </td>
                            </tr>

                            <!-- Warning -->
                            <tr>
                                <td style="color:#6b7280; font-size:14px;">
                                    <p>
                                        If you didn’t request this code, you can safely ignore this email.
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="padding-top:25px; color:#9ca3af; font-size:12px;">
                                    <p style="margin:0;">
                                        © ' . date('Y') . ' Moto. All rights reserved.
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';



  $response = Http::withHeaders([
   'api-key' => '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',
   'Content-Type' => 'application/json',
   'accept' => 'application/json',
  ])->post('https://api.brevo.com/v3/smtp/email', [
   'sender' => [
    'email' => 'moto80601030@gmail.com',
    'name'  => 'Moto',
   ],
   'to' => [
    ['email' => $email],
   ],
   'subject' => 'Your OTP Verification Code',
   'htmlContent' => $html,
  ]);

  return $response->successful();
 }
}
