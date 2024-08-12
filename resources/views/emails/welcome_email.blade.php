<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Our Repair Service</title>
</head>
<body>
    <h1>Welcome!</h1>
    <p>Thank you for bringing your {{ $repair->device_type }} in for repair.</p>
    <p>Repair Number: {{ $repair->repair_number }}</p>
    <p>We will update you on the status of your repair shortly.</p>
    <p>Best regards,<br>Your Repair Service Team</p>
</body>
</html>
