<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        @page {
            size: 38mm 90mm; /* Set label dimensions */
            margin: 0; /* No margins for accurate label size */
        }
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            height: 100vh;
        }
        .label {
            width: 38mm;
            height: 90mm;
            box-sizing: border-box;
            text-align: center;
            font-size: 7px;
            padding: 5mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border: 1px solid transparent; /* No visible border */
            overflow: hidden; /* Prevent overflow */
            white-space: nowrap; /* Ensure content stays on one line */
        }
    </style>
</head>
<body>
    <!-- Label 1 -->
    <div class="label">
        <span><strong>R/N:</strong> {{ $repair_number }}</span>
    </div>

    <!-- Label 2 -->
    <div class="label">
        <span><strong>R/N:</strong> {{ $repair_number }}</span>
    </div>

    <script>
        // Automatically trigger the print dialog on page load
        window.onload = function () {
            window.print();
        }
    </script>
</body>
</html>
