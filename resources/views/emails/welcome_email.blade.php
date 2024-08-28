@component('mail::message')
# Repair Created

Dear {{ $repair->customer->name }},

Your repair with number **{{ $repair->repair_number }}** has been successfully created.

{{ $customMessage }}


### Device Information
- **Device Type:** {{ ucfirst($repair->device_type) }}
- **Status:** {{ ucfirst($repair->status) }}
- **Estimated Cost:** ${{ number_format($repair->estimated_cost, 2) }}

@component('mail::button', ['url' => ''])
View Repair Details
@endcomponent

Thank you for choosing our service.

Best regards,<br>
{{ config('app.name') }}
@endcomponent
