<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOB POSTING NOTIFICATION!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 100px;
            height: auto;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            color: #666;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="https://example.com/logo.png" alt="Logo">
    </div>
    <h1>Dear {{ $fullName }}</h1>
    <p>You are receiving this email because you have requested to reset your password for your account.</p>
    <p>To log in to your account, please use the following temporary password:</p>
    <p>Temporary Password: {{$token }}</p>
    <p>We advise you to log in using this temporary password and then update it to a more secure and personalized password</p>
    <p>If you did not request a password reset, please disregard this email. Your account is secure, and no changes have been made.

        If you have any questions or need further assistance, please don't hesitate to contact our support team at [Support Email Address].

        Thank you for using {{$appname}}.

        Best regards,
        {{$appname}} Team</p>
    
    <div style="text-align: center;">
        <a href="https://example.com" class="btn">Browse Services</a>
    </div>
</div>
</body>
</html>
