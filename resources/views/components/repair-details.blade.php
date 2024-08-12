<div class="container mt-5">
    <h2 class="mb-4 text-center">Repair Details</h2>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Repair Number:</strong> {{ $record->repair_number }}</p>
            <p><strong>Customer:</strong> {{ $record->customer->name }}</p>
            <p><strong>Device Type:</strong> {{ ucfirst($record->device_type) }}</p>
            <p><strong>Status:</strong> {{ $record->status }}</p>
            <p><strong>Quoted Price:</strong> ${{ $record->quoted_price }}</p>
            <p><strong>Finalized Price:</strong> ${{ $record->finalized_price }}</p>
            <p><strong>Location:</strong> {{ $record->location }}</p>
        </div>
        <div class="col-md-6">
            @if($record->device_type === 'mobile')
            <h3 class="mt-4">Mobile Device Details</h3>
            <div class="row">
                <div class="col-6">
                    <p><strong>Make:</strong> {{ $record->make }}</p>
                    <p><strong>Model:</strong> {{ $record->model }}</p>
                    <p><strong>IMEI:</strong> {{ $record->imei }}</p>
                    <p><strong>Network:</strong> {{ $record->network }}</p>
                    <p><strong>Passcode:</strong> {{ $record->passcode }}</p>
                    <p><strong>Issue Description:</strong> {{ $record->issue_description }}</p>
                    <p><strong>Estimated Cost:</strong> ${{ $record->estimated_cost }}</p>
                </div>
                <div class="col-6">
                    <p><strong>Power Up:</strong> {{ $record->power_up ? 'Yes' : 'No' }}</p>
                    <p><strong>Lens / LCD Damage:</strong> {{ $record->lens_lcd_damage ? 'Yes' : 'No' }}</p>
                    <p><strong>Missing Parts:</strong> {{ $record->missing_parts ? 'Yes' : 'No' }}</p>
                    <p><strong>Liquid Damage:</strong> {{ $record->liquid_damage ? 'Yes' : 'No' }}</p>
                    <p><strong>Tampered:</strong> {{ $record->tampered ? 'Yes' : 'No' }}</p>
                    <p><strong>Button Functions OK:</strong> {{ $record->button_functions_ok ? 'Yes' : 'No' }}</p>
                    <p><strong>Camera Lens / Back Damage:</strong> {{ $record->camera_lens_damage ? 'Yes' : 'No' }}</p>
                    <p><strong>SIM and SD Removed:</strong> {{ $record->sim_sd_removed ? 'Yes' : 'No' }}</p>
                    <p><strong>Risk to Back:</strong> {{ $record->risk_to_back ? 'Yes' : 'No' }}</p>
                    <p><strong>Risk to LCD:</strong> {{ $record->risk_to_lcd ? 'Yes' : 'No' }}</p>
                    <p><strong>Risk to Biometrics:</strong> {{ $record->risk_to_biometrics ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
