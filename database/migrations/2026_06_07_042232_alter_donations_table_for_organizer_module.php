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
        Schema::table('donations', function ($table) {

            $table->string('contributor')
                ->nullable()
                ->after('campaign_id');

            $table->string('transaction_id')
                ->nullable()
                ->after('contributor');

            $table->string('campaign_type')
                ->nullable()
                ->after('transaction_id');

            $table->enum(
                'status',
                [
                    'Pending',
                    'Allocated'
                ]
            )
                ->default('Pending')
                ->after('campaign_type');

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
