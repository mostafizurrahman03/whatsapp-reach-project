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
        Schema::create('client_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('api_key')->unique();
            $table->string('secret_key');
            $table->integer('tps')->default(5);
            $table->json('service_routing')->nullable(); // {"sms":"reve","whatsapp":"meta"}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_configurations');
    }
};
