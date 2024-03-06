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

    <h1>Dear {{ $credential['client_fullname'] }},</h1>

    <p>We hope this message finds you well.</p>

    <p>We are excited to inform you that a talented individual has submitted a proposal for your job posting "{{ $credential['project_title'] }}". We're thrilled about the opportunity this presents to you and your project.</p>

    <p>
        <strong>Job Title:</strong> {{ $credential['project_title'] }}<br>
        <strong>Proposal Submitted By:</strong> {{ $credential['talent_fullname'] }}
    </p>

    <p>
        Here's a brief overview of the proposal:<br>
        [{{ $credential['proposal_cover_info'] }}.]
    </p>

    <p>
        To review the full proposal and engage with the talented individual, you can access it directly on our platform by clicking the link below:
        <br>
        <a href="{{ $credential['link_to_proposal'] }}">Browse proposal</a>
    </p>

    <p>
        We encourage you to take a moment to review the proposal thoroughly. Should you have any questions or need further assistance, please don't hesitate to reach out to our support team.
    </p>

    <p>
        Thank you for choosing our platform for your hiring needs. We're committed to helping you find the perfect match for your project.
    </p>

    <p>Best regards,</p>
    <p>Team Designox Team {{ config('services.app_config.app_name')}}</p>

    <div style="text-align: center;">
        <a href="https://example.com" class="btn">Browse Services</a>
    </div>
</div>

</body>
</html>
