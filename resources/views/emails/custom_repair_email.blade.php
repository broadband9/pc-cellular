<!DOCTYPE html>
<html>
<head>
    <title>Repair Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Hello {{ $repair->customer->name }}</h2>
        <p>A repair has been created with the following details:</p>
        <ul>
            <li><strong>Repair Number:</strong> {{ $repair->repair_number }}</li>
            <li><strong>Device Type:</strong> {{ $repair->device_type }}</li>
            <li><strong>Status:</strong> {{ $repair->status->name }}</li>
            <li><strong>Location:</strong> {{ $repair->location->name }}</li>
            <li><strong>Estimated Price:</strong> {{ $repair->estimated_cost }}</li>
            <li><strong>Additional Notes:</strong> {{ $repair->issue_description ?? 'N/A' }}</li>
        </ul>
        <p>If you have any questions, feel free to call us on 01234567898.</p>
        <p>Many thanks,</p>
        <p>PC Cellular</p>
    </div>
</body>
</html>