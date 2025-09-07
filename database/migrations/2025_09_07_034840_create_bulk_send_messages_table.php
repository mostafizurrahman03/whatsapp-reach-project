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
        Schema::create('bulk_send_messages', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->foreign('device_id')
                ->references('device_id')
                ->on('my_whatsapp_devices')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message'); // message body
            $table->boolean('is_sent')->default(false); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_send_messages');
    }
};
