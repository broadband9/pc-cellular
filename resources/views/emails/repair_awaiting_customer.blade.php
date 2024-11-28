<!DOCTYPE html>
<html>
<head>
    <title>Repair Awaiting Customer Response</title>
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
        
        <p>We need your input to proceed with the repair of your device.</p>
        
        <h3>Repair Details:</h3>
        <ul>
            <li><strong>Repair Number:</strong> {{ $repair->repair_number }}</li>
            <li><strong>Device Type:</strong> {{ $repair->device_type }}</li>
            <li><strong>Make/Model:</strong> {{ $repair->make->name }} {{ $repair->model }}</li>
        </ul>
        
        <p>We require additional information or confirmation from you to continue with the repair process. Please contact us at your earliest convenience.</p>
        
        <p>If you have any questions, please call us at 01234 567 898.</p>
        
        <p>Thank you,<br>PC Cellular Team</p>
    </div>
</body>
</html>