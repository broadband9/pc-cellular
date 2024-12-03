<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .label {
            width: 144px; /* 38mm in pixels */
            height: 90px; /* Adjusted for appropriate label height */
            border: none; /* Removed border */
            padding: 5px;
            box-sizing: border-box;
            text-align: center;
            font-size: 12px; /* Small font size */
            margin: 5px;
        }
        .container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="label">
            <strong>R/N:</strong> {{ $repair_number }}
        </div>
        <div class="label">
            <strong>R/N:</strong> {{ $repair_number }}
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
