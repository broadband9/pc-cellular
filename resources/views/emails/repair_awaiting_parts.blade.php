<!DOCTYPE html>
<html>
<head>
    <title>Repair Awaiting Parts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .content {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Hello {{ $repair->customer->name }}</h2>
        
        <p>We wanted to update you on the status of your repair. Currently, we are waiting for specific parts to complete the repair.</p>
        
        <h3>Repair Details:</h3>
        <ul>
            <li><strong>Repair Number:</strong> {{ $repair->repair_number }}</li>
            <li><strong>Device Type:</strong> {{ $repair->device_type }}</li>
            <li><strong>Make/Model:</strong> {{ $repair->make->name }} {{ $repair->model }}</li>
        </ul>
        
        <p>We will notify you as soon as the parts arrive and we can proceed with the repair.</p>
        
        <p>If you have any questions, please contact us at 01234 567 898.</p>
        
        <p>Thank you for your patience,<br>PC Cellular Team</p>
    </div>
</body>
</html>