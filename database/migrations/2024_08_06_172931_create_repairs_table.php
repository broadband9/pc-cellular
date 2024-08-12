<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('device_type');
            $table->string('repair_number')->unique();
            $table->string('status');
            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->decimal('finalized_price', 10, 2)->nullable();
            $table->string('location')->nullable();

            // General fields for all devices
            $table->text('issue_description')->nullable();

            // Fields for mobile phones
            $table->string('mobile_make')->nullable();
            $table->string('mobile_model')->nullable();
            $table->string('mobile_imei')->nullable();
            $table->string('mobile_network')->nullable();
            $table->string('mobile_passcode')->nullable();
            $table->boolean('mobile_power_up')->default(false);
            $table->boolean('mobile_lens_lcd_damage')->default(false);
            $table->boolean('mobile_missing_parts')->default(false);
            $table->boolean('mobile_liquid_damage')->default(false);
            $table->boolean('mobile_tampered')->default(false);
            $table->boolean('mobile_button_functions_ok')->default(false);
            $table->boolean('mobile_camera_lens_damage')->default(false);
            $table->boolean('mobile_sim_sd_removed')->default(false);
            $table->boolean('mobile_risk_to_back')->default(false);
            $table->boolean('mobile_risk_to_lcd')->default(false);
            $table->boolean('mobile_risk_to_biometrics')->default(false);

            // Fields for laptops
            $table->string('laptop_make')->nullable();
            $table->string('laptop_model')->nullable();
            $table->string('laptop_serial_number')->nullable();
            $table->string('laptop_os')->nullable();
            $table->string('laptop_warranty')->nullable();
            $table->text('laptop_issue_description')->nullable();
            $table->decimal('laptop_estimated_cost', 10, 2)->nullable();
            $table->string('laptop_location')->nullable();

            // Add more fields for other device types as needed...

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repairs');
    }
};
