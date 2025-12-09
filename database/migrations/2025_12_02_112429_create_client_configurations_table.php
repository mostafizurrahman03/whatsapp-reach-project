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
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Optional if client = user
            $table->string('client_api_key')->unique();
            $table->string('client_secret_key');
            $table->decimal('balance', 14, 2)->default(0);
            $table->decimal('rate_per_sms', 8, 2)->default(0.00);
            $table->integer('tps')->default(5);
            $table->json('service_routing')->nullable(); // {"sms":"reve","whatsapp":"meta"}
            $table->json('allowed_ips')->nullable(); // ["103.10.12.45"]
            $table->boolean('is_active')->default(true);
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
