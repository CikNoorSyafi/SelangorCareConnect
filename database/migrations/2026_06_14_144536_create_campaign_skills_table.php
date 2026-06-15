<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('campaign_skills', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('campaign_id');

            $table->unsignedBigInteger('skill_id');

            $table->timestamps();

            $table->foreign('campaign_id')
                ->references('id')
                ->on('campaigns')
                ->onDelete('cascade');

            $table->foreign('skill_id')
                ->references('id')
                ->on('skills')
                ->onDelete('cascade');

            $table->unique([
                'campaign_id',
                'skill_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_skills');
    }
};