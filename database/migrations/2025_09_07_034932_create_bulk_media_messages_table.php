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
        Schema::create('bulk_media_messages', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->foreign('device_id')
                ->references('device_id')
                ->on('my_whatsapp_devices')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message')->nullable(); // message body
            $table->string('caption')->nullable();  
            $table->string('media_url')->nullable();  
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_media_messages');
    }
};
