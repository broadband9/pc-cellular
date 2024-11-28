<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Repair Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .print-btn {
            background-color: #3C4B64;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px 0;
            cursor: pointer;
        }
        .print-btn:hover {
            background-color: #2A3B5A;
        }
        /* Add print styles here */
        @media print {
            .print-btn {
                display: none; /* Hide print button when printing */
            }
        }
        .signature-section {
            display: flex;
            align-items: flex-start;
            margin-top: 20px;
        }
        .signature-label {
            margin-right: 20px;
        }
        .signature-image {
            max-height: 150px;
            max-width: 200px;
            display: block;
        }
        .signature-line {
            width: 200px;
            height: 2px;
            background-color: black;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Repair Details</h2>

        <div class="text-center mb-4 float-right">
            <button class="print-btn" onclick="window.print()">Print</button>
        </div>

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
                    <td>Location</td>
                    <td>{{ $record->location->name }}</td>
                </tr>
                <tr>
                    <td>Make</td>
                    <td>{{ $record->make->name }}</td>
                </tr>
                <tr>
                    <td>Model</td>
                    <td>{{ $record->model }}</td>
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
                    <td>Finalized Price</td>
                    <td>${{ $record->finalized_price }}</td>
                </tr>
                <tr>
                    <td>Power Up</td>
                    <td>{{ $record->power_up ? 'Yes' : 'No' }}</td>
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

                @if(in_array($record->device_type, ['mobile', 'tablet']))
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
                    <td>Lens / LCD Damage</td>
                    <td>{{ $record->lens_lcd_damage ? 'Yes' : 'No' }}</td>
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

                @if($record->device_type === 'laptop')
                <tr>
                    <td>Operating System</td>
                    <td>{{ $record->operating_system }}</td>
                </tr>
                <tr>
                    <td>RAM</td>
                    <td>{{ $record->ram }}</td>
                </tr>
                <tr>
                    <td>Storage</td>
                    <td>{{ $record->storage }}</td>
                </tr>
                <tr>
                    <td>Keyboard Functional</td>
                    <td>{{ $record->keyboard_functional ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Trackpad Functional</td>
                    <td>{{ $record->trackpad_functional ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Screen Damage</td>
                    <td>{{ $record->screen_damage ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td>Hinge Damage</td>
                    <td>{{ $record->hinge_damage ? 'Yes' : 'No' }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="signature-section float-right">
            <div class="signature-label">Customer Signature:</div>
            <div>
                <img src="{{ $record->customer_signature }}" alt="Customer Signature" class="signature-image">
            </div>
        </div>

        <!-- Print Document Form -->
        {{-- <div class="mt-4">
            <h4>Print Document</h4>
            <form action="{{ route('print.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="printer_name">Select Printer:</label>
                    <select name="printer_name" id="printer_name" class="form-control" required>
                        @foreach($printers as $printer)
                            <option value="{{ $printer }}">{{ $printer }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="document_path">Upload Document:</label>
                    <input type="file" name="document_path" id="document_path" class="form-control" accept=".pdf,.txt,.doc,.docx" required>
                </div>
                <button type="submit" class="btn btn-primary">Print Document</button>
            </form>
        </div> --}}

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
