<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('device_type');
            $table->string('repair_number')->unique();
            $table->unsignedBigInteger('status_id'); 
            $table->unsignedBigInteger('location_id'); 
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('imei')->nullable();
            $table->string('network')->nullable();
            $table->string('passcode')->nullable();
            $table->text('issue_description')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('finalized_price', 10, 2)->nullable();
            
            // Mobile-specific fields
            $table->boolean('power_up')->default(false);
            $table->boolean('lens_lcd_damage')->default(false);
            $table->boolean('missing_parts')->default(false);
            $table->boolean('liquid_damage')->default(false);
            $table->boolean('tampered')->default(false);
            $table->boolean('button_functions_ok')->default(false);
            $table->boolean('camera_lens_damage')->default(false);
            $table->boolean('sim_sd_removed')->default(false);
            $table->boolean('risk_to_back')->default(false);
            $table->boolean('risk_to_lcd')->default(false);
            $table->boolean('risk_to_biometrics')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repairs');
    }
};
