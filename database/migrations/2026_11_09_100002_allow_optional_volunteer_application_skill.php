<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('volunteer_applications', 'skill')) {
            Schema::table('volunteer_applications', function (Blueprint $table) {
                $table->string('skill')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('volunteer_applications', 'skill')) {
            DB::table('volunteer_applications')
                ->whereNull('skill')
                ->update(['skill' => '']);

            Schema::table('volunteer_applications', function (Blueprint $table) {
                $table->string('skill')->nullable(false)->change();
            });
        }
    }
};
