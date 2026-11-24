<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The organizer workflow uses Fundraising, Recruiting, In Progress and
     * Completed. Store campaign status as a string so those lifecycle values
     * are supported by the database schema.
     */
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('status', 30)->default('Fundraising')->change();
        });
    }

    public function down(): void
    {
        // Intentionally left irreversible: existing campaigns may already
        // use lifecycle values that were not present in the original enum.
    }
};
