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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // owner
            $table->string('name', 150);
            $table->enum('channel', ['whatsapp','sms','email'])->default('whatsapp');
            $table->enum('status', ['draft','scheduled','running','completed','failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        
            $table->softDeletes(); // optional, future-safe
            
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
