<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('status_id')->constrained('repair_statuses')->onDelete('restrict');
            $table->foreignId('location_id')->constrained()->onDelete('restrict');
            $table->foreignId('make_id')->constrained()->onDelete('restrict');

            $table->enum('device_type', ['mobile', 'tablet', 'laptop']);
            $table->string('repair_number')->unique();
            $table->string('model')->nullable();
            $table->string('imei')->nullable();
            $table->string('network')->nullable();
            $table->string('passcode')->nullable();
            $table->text('issue_description')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('finalized_price', 10, 2)->nullable();
            
            // Common fields for all device types
            $table->boolean('power_up')->default(false);
            $table->boolean('missing_parts')->default(false);
            $table->boolean('liquid_damage')->default(false);
            $table->boolean('tampered')->default(false);

            // Mobile and Tablet specific fields
            $table->boolean('lens_lcd_damage')->default(false);
            $table->boolean('button_functions_ok')->default(false);
            $table->boolean('camera_lens_damage')->default(false);
            $table->boolean('sim_sd_removed')->default(false);
            $table->boolean('risk_to_back')->default(false);
            $table->boolean('risk_to_lcd')->default(false);
            $table->boolean('risk_to_biometrics')->default(false);

            // Laptop specific fields
            $table->boolean('keyboard_functional')->default(false);
            $table->boolean('trackpad_functional')->default(false);
            $table->boolean('screen_damage')->default(false);
            $table->boolean('hinge_damage')->default(false);
            $table->string('operating_system')->nullable();
            $table->string('ram')->nullable();
            $table->string('storage')->nullable();

            $table->text('customer_signature')->nullable();
            $table->boolean('send_email')->default(true);
            $table->text('email_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};