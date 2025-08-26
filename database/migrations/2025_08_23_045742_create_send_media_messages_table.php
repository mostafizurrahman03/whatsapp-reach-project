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
        Schema::create('send_media_messages', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');   // sender device id column added
            $table->string('number');
            $table->text('message');
            $table->string('caption')->nullable();  // new column added
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
        Schema::dropIfExists('send_media_messages');
    }
};
