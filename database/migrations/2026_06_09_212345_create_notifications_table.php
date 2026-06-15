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
        Schema::create('notifications', function ($table) {

            $table->id();

            $table->string('title');

            $table->text('message');

            $table->enum(
                'type',
                [
                    'System',
                    'Manual'
                ]
            );

            $table->enum(
                'audience',
                [
                    'All Volunteers',
                    'Campaign Volunteers',
                    'All Donors',
                    'Campaign Donors'
                ]
            );

            $table->foreignId('campaign_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum(
                'status',
                [
                    'Draft',
                    'Sent',
                    'Delivered',
                    'Failed'
                ]
            )
                ->default('Draft');

            $table->integer('recipients')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
