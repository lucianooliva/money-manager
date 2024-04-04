<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <tr>
      <td align="center" style="background-color: #f3f3f3; padding: 40px;">
        <img src="https://your-domain.com/logo.png" alt="Logo" style="max-width: 150px;">
      </td>
    </tr>
    <tr>
      <td style="background-color: #ffffff; padding: 40px;">
        <h2 style="margin-top: 0;">Verify Your Email Address</h2>
        <p style="margin-bottom: 20px;">Thank you for registering. Please click the button below to verify your email address:</p>
        <table cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" bgcolor="#007bff" style="border-radius: 5px;">
              <a href="{{ $verificationUrl }}" target="_blank" style="font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 10px 20px; display: inline-block;">Verify Email Address</a>
            </td>
          </tr>
        </table>
        <p style="margin-top: 20px;">If you didn't create an account, no further action is required.</p>
      </td>
    </tr>
    <tr>
        <td align="center" bgcolor="#f3f3f3" style="padding: 20px;">
            <p style="margin: 0;">&copy; 2024 Your Company. All rights reserved.</p>
        </td>
    </tr>
  </table>

</body>
</html>