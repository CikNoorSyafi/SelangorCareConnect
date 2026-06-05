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
        Schema::create(
            'volunteer_applications',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('user_id');

                $table->foreignId('campaign_id');

                $table->string('shift');

                $table->string('skill');

                $table->text('notes')->nullable();

                $table->enum(
                    'status',
                    [
                        'Pending',
                        'Approved',
                        'Rejected',
                        'Withdrawn'
                    ]
                )->default('Pending');

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
};
