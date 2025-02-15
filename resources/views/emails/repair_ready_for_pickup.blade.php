<!DOCTYPE html>
<html>
<head>
    <title>Repair Ready for Pickup</title>
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
        
        <p>Great news! Your repair is now ready for pickup.</p>
        
        <h3>Repair Details:</h3>
        <ul>
            <li><strong>Repair Number:</strong> {{ $repair->repair_number }}</li>
            <li><strong>Device Type:</strong> {{ $repair->device_type }}</li>
            <li><strong>Make/Model:</strong> {{ $repair->make->name }} {{ $repair->model }}</li>
            <li><strong>Finalized Price:</strong> £{{ number_format($repair->finalized_price, 2) }}</li>
        </ul>
        
        <p>Please visit our store to collect your repaired device. Don't forget to bring a valid ID.</p>
        
        <p>If you have any questions, please contact us at 01234 567 898.</p>
        
        <p>Thank you,<br>PC Cellular Team</p>
    </div>
</body>
</html>