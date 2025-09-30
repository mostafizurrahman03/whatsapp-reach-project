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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // subscriber (owner)
            $table->string('name', 150)->nullable();
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('source', 100)->nullable();
            $table->enum('status', ['new','contacted','converted','lost'])->default('new');
            $table->timestamps();
        
            $table->unique(['user_id','phone']); // এক ইউজারের জন্য unique
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
