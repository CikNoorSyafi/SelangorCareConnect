<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // General Fund contributions do not belong to a campaign.
            $table->unsignedBigInteger('campaign_id')->nullable()->change();

            // The donor payment simulation records Pending, Allocated and Failed.
            $table->string('status', 20)->default('Pending')->change();
        });
    }

    public function down(): void
    {
        // Intentionally irreversible: General Fund donations may have a null campaign_id,
        // and Failed donation records may exist after this migration.
    }
};
