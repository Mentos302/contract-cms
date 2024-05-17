<!DOCTYPE html>
<html>

<head>
    <title>CMS Notification - Contract Successfully Added</title>
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
            <h1>Contract Successfully Added</h1>
        </div>
        <div class="content">
            <p>Hi <strong>{{ $userFirstName }}</strong>,</p>
            <p>Thank you for adding your <strong>{{ $manufacturerName }}</strong> Contract#
                <strong>{{ $contractNumber }}</strong> to Sivility CMS. Please
                review the details below:</p>
            @if ($contract->manufacturer)
                <p><strong>[MFR]:</strong> {{ $contract->manufacturer->name }}</p>
            @endif

            @if ($contract->type)
                <p><strong>[Type of Contract/License]:</strong> {{ $contract->type->name }}</p>
            @endif

            @if ($contract->term)
                <p><strong>[Term]:</strong> {{ $contract->term->name }}</p>
            @endif

            @if ($contract->start_date)
                <p><strong>[Start Date]:</strong> {{ $contract->start_date }}</p>
            @endif

            @if ($contract->end_date)
                <p><strong>[End Date]:</strong> {{ $contract->end_date }}</p>
            @endif

            @if ($contract->location)
                <p><strong>[Location]:</strong> {{ $contract->location }}</p>
            @endif

            @if ($contract->name)
                <p><strong>[Name of device]:</strong> {{ $contract->name }}</p>
            @endif

            <p>If you need to make any changes, you can login to <a href="https://cms.sivility.com/login"
                    class="btn">here</a>.</p>
        </div>

        <div class="footer">
            <img src="https://1b3042.a2cdn1.secureserver.net/wp-content/uploads/2023/08/Sivility-logo-3-21.png"
                style="width: 100%; max-width: 320px" alt="">
        </div>
    </div>
</body>

</html>
