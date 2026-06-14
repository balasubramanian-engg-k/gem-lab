<!DOCTYPE html>
<html>
<head>
    <title>Membership renewed — MDCC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            color: #000;
            font-size: 20px;
            font-weight: bold;
            border-radius: 8px 8px 0 0;
        }
        .content {
            text-align: center;
            font-size: 16px;
            color: #333;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Membership renewed</div>
        <div class="content">
            <p>Hi <strong>{{ $details['name'] }}</strong>,</p>
            <p>Your MDCC membership has been <strong>renewed</strong> successfully for the current financial year.</p>
            <p>Your MDCC Id: <b>{{ $details['mdccid'] }}</b></p>
            <p>Category: <b>{{ $details['playerType'] }}</b></p>
            <p>Thank you for continuing with <a href="https://maduraichesscircle.com/">Maduraichesscircle.com</a>. For any questions, write to <a href="mailto:maduraichesscircle@gmail.com">maduraichesscircle@gmail.com</a>.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>The Team</p>
            <p>&copy; {{ date('Y') }} MDCC. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
