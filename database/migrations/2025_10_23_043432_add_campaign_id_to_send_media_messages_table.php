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
        Schema::table('bulk_media_messages', function (Blueprint $table) {
            // $table->foreignId('campaign_id')
            //   ->constrained('campaigns')
            //   ->onDelete('cascade')
            //   ->after('user_id'); // optional positioning

            // Campaign relation
            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained('campaigns')
                ->nullOnDelete()
                ->after('user_id');
                
            
            // Soft delete column
            $table->softDeletes(); // deleted_at column add করবে
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_media_messages', function (Blueprint $table) {
            $table->dropForeign(['campaign_id']);
            $table->dropColumn('campaign_id');
            $table->dropSoftDeletes(); // deleted_at column drop করবে
        });
    }
};
