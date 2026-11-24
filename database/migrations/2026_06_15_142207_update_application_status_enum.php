<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('volunteer_applications', function (Blueprint $table) {

            DB::statement("
        ALTER TABLE volunteer_applications
        MODIFY status ENUM(
            'Pending',
            'Approved',
            'Rejected',
            'Withdrawn'
        ) DEFAULT 'Pending'
    ");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
