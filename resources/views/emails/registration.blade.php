<!DOCTYPE html>
<html>
<head>
    <title>Welcome to MDCC</title>
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
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #fff;
            background: #007bff;
            text-decoration: none;
            border-radius: 5px;
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
        <div class="header">Welcome to MDCC</div>
        <div class="content">
            <p>Hi <strong>{{ $details['name'] }}</strong>,</p>
            <p>Your MDCC Id is <b>{{ $details['mdccid'] }}</b></p>
            <p>Thank you for registering as <b>({{ $details['playerType'] }})</b> in <a href="https://maduraichesscircle.com/">Maduraichesscircle.com</a> for the current financial year. For any doubts or clarification pls mail at <a href="mailto:maduraichesscircle@gmail.com">maduraichesscircle@gmail.com</a>...</p>
        </div>
        <div class="footer">
            <p>Best regards, <br> The Team</p>
            <p>&copy; {{ date('Y') }} MDCC. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
