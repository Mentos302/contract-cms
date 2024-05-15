<!DOCTYPE html>
<html>

<head>
    <title>Welcome to our platform</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            box-sizing: border-box;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #1a67c2;
        }

        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666666;
        }

        .footer p {
            margin: 10px 0;
        }

        a.btn {
            color: #1a67c2;
            text-decoration: none;
        }

        @media only screen and (max-width: 600px) {
            .header h1 {
                font-size: 24px;
            }

            .container {
                max-width: 320px;
                margin: 10px auto;
            }

            .content p {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to CMS by Sivility Systems</h1>
        </div>
        <div class="content">
            <p>Dear user,</p>
            <p>Your account has been created successfully. Here are your login details:</p>
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password:</strong> <span
                    style="background-color: #f2f2f2; padding: 5px 10px; border-radius: 5px;">{{ $password }}</span>
            </p>
            <p style="font-size: 12px;">Please note that this is an automatically generated password. We recommend that
                you change it in your
                account settings after logging in for the first time.</p>
            <p>You can login <a href="{{ route('login') }}" class="btn">here</a>.</p>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>Sivility Systems</p>
        </div>
    </div>
</body>

</html>
