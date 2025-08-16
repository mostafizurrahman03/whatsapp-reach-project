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
        Schema::create('my_whatsapp_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device_id')->unique();  
            $table->string('device_name')->nullable();    
            $table->string('phone_number')->nullable(); 

            // QR Code
            $table->text('qr_code')->nullable();      
            $table->longText('qr_image')->nullable();  

            // Session Data
            $table->longText('session_data')->nullable(); 

            // Connection Status
            $table->enum('status', ['pending', 'connected', 'disconnected'])->default('pending');
            $table->boolean('connected')->default(false); 

            // Tracking times
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamp('last_disconnected_at')->nullable();

            $table->index('status');
            $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_whatsapp_devices');
    }
};
