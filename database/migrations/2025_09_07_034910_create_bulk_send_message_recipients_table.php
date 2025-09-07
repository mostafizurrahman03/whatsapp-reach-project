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
        Schema::create('bulk_send_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bulk_send_message_id')
                  ->constrained('bulk_send_messages')
                  ->onDelete('cascade');
            $table->string('number'); // receiver number
            $table->boolean('is_sent')->default(false); // per number delivery status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_send_message_recipients');
    }
};
