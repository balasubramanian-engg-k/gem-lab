<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gem Certificate</title>
    <style>
        body {
            width: 8.5cm;
            height: 5.5cm;
            margin: 0;
            padding: 10px;
            font-family: Arial, sans-serif;
            border: 2px solid #000;
        }
        h2 {
            margin: 0;
            font-size: 16px;
        }
        .details {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h2>{{ $gem->name }}</h2>
    <div class="details">
        Weight: {{ $gem->weight }} ct <br>
        Color: {{ $gem->color }} <br>
        ID: {{ $gem->id }}
    </div>
</body>
</html>
