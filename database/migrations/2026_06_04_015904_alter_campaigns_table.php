<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {

            $table->renameColumn('title', 'name');

            $table->renameColumn('category', 'type');

            $table->dropColumn('campaign_date');

            $table->decimal(
                'target',
                12,
                2
            )->nullable();

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();

        });
    }

    public function down(): void
    {
        //
    }
};