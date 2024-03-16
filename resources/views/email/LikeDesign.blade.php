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

    <h1>Dear {{$fullName }},</h1>

    <p>We hope this message finds you well.</p>

    <p>We are excited to inform you that a user has liked your design on our platform. It's always rewarding to receive recognition for your creative work!</p>

    <p>
        <strong>Design Liked:</strong> {{$project_title}}<br>
    </p>

    <p>
        To view the liked design and engage with the user, you can access it directly on our platform by clicking the link below:
        <br>
        <a href="{{ $designUrl }}">View Liked Design</a>
    </p>

    <p>
        We encourage you to explore further and connect with others in our community. Should you have any questions or need assistance, please don't hesitate to reach out to our support team.
    </p>

    <p>
        Thank you for being part of our platform and sharing your creativity. We look forward to seeing more of your work!
    </p>

    <p>Best regards,</p>
    <p>Team Designox Team {{ config('services.app_config.app_name')}}</p>

    <div style="text-align: center;">
        <a href="https://example.com" class="btn">Browse Services</a>
    </div>
</div>


</body>
</html>
