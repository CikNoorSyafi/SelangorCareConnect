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
        Schema::table('users', function ($table) {

            $table->string('phone')
                ->nullable()
                ->after('role');

            $table->string('organization')
                ->nullable()
                ->after('phone');

            $table->boolean('campaign_notifications')
                ->default(true)
                ->after('organization');

            $table->boolean('volunteer_notifications')
                ->default(true)
                ->after('campaign_notifications');

            $table->boolean('donation_notifications')
                ->default(true)
                ->after('volunteer_notifications');

            $table->boolean('communication_notifications')
                ->default(true)
                ->after('donation_notifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
