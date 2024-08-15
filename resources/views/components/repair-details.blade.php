<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .print-btn {
            background-color: #3C4B64; /* Default Filament color */
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px 0;
            cursor: pointer;
        }
        .print-btn:hover {
            background-color: #2A3B5A; /* Darker Filament color */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Repair Details</h2>

        <!-- Print Button Above Table -->
        <div class="text-center mb-4 float-right">
            <button class="print-btn" onclick="window.print()">Print</button>
        </div>

        <!-- Details Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Field</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Repair Number</td>
                    <td>{{ $record->repair_number }}</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>{{ $record->customer->name }}</td>
                </tr>
                <tr>
                    <td>Device Type</td>
                    <td>{{ ucfirst($record->device_type) }}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{ $record->status->name }}</td>
                </tr>
                <tr>
                    <td>Quoted Price</td>
                    <td>${{ $record->quoted_price }}</td>
                </tr>
                <tr>
                    <td>Finalized Price</td>
                    <td>${{ $record->finalized_price }}</td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td>{{ $record->location->name }}</td>
                </tr>
                @if($record->device_type === 'mobile')
                <tr>
                    <td>Make</td>
                    <td>{{ $record->make }}</td>
                </tr>
                <tr>
                    <td>Model</td>
                    <td>{{ $record->model }}</td>
                </tr>
                <tr>
                    <td>IMEI</td>
                    <td>{{ $record->imei }}</td>
                </tr>
                <tr>
                    <td>Network</td>
                    <td>{{ $record->network }}</td>
                </tr>
                <tr>
                    <td>Passcode</td>
                    <td>{{ $record->passcode }}</td>
                </tr>
                <tr>
                    <td>Issue Description</td>
                    <td>{{ $record->issue_description }}</td>
                </tr>
                <tr>
                    <td>Estimated Cost</td>
                    <td>${{ $record->estimated_cost }}</td>
                </tr>
                <tr>
                    <td>Power Up</td>
                    <td>{{ $record->power_up ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Lens / LCD Damage</td>
                    <td>{{ $record->lens_lcd_damage ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Missing Parts</td>
                    <td>{{ $record->missing_parts ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Liquid Damage</td>
                    <td>{{ $record->liquid_damage ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Tampered</td>
                    <td>{{ $record->tampered ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Button Functions OK</td>
                    <td>{{ $record->button_functions_ok ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Camera Lens / Back Damage</td>
                    <td>{{ $record->camera_lens_damage ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>SIM and SD Removed</td>
                    <td>{{ $record->sim_sd_removed ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Risk to Back</td>
                    <td>{{ $record->risk_to_back ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Risk to LCD</td>
                    <td>{{ $record->risk_to_lcd ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Risk to Biometrics</td>
                    <td>{{ $record->risk_to_biometrics ? 'Yes' : 'No' }}</td>
                </tr>
                @endif
            </tbody>
        </table>

      
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
