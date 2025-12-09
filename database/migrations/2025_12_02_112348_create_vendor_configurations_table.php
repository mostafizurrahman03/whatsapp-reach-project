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
        Schema::create('vendor_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('vendor_name');
            $table->string('base_url');
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->integer('tps')->default(10);
            $table->json('extra_config')->nullable(); // extra settings
            $table->json('ip_whitelist')->nullable(); // ["103.10.12.45"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_configurations');
    }
};
