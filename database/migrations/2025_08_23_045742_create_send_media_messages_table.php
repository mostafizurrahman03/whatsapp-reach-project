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
            $table->string('device_id');   // relation column
            $table->foreign('device_id')   // foreign key constraint
                ->references('device_id')
                ->on('my_whatsapp_devices')
                ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
