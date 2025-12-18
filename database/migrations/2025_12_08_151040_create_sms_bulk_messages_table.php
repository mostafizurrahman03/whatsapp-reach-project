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
            // $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            // $table->string('service'); // sms / whatsapp / voice / ivr
            // $table->text('content'); // message body
            // $table->json('recipients'); // ["8801xxxxxxx","8801yyyyyyy"]
            // $table->string('status')->default('pending'); // pending, sent, failed
            // $table->json('response')->nullable(); // API response

             // Client / User
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('vendor_configuration_id')
                ->nullable()
                ->constrained('vendor_configurations')
                ->nullOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('sender_id');
            $table->text('content');
            $table->json('recipients');
            $table->unsignedInteger('total_recipients')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->enum('status', [
                'pending',
                'processing',
                'sent',
                'partial',
                'failed'
            ])->default('pending');
            $table->json('response')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
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




