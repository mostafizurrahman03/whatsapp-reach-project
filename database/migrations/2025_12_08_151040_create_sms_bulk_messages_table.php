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
        Schema::create('sms_bulk_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->string('service'); // sms / whatsapp / voice / ivr
            $table->text('content'); // message body
            $table->json('recipients'); // ["8801xxxxxxx","8801yyyyyyy"]
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->json('response')->nullable(); // API response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_bulk_messages');
    }
};




