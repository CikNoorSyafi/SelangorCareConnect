<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('attendance', 'status')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->enum('status', ['Present', 'Absent'])
                    ->default('Present')
                    ->after('hours_served');
            });
        }

        Schema::table('attendance', function (Blueprint $table) {
            $table->time('check_in')->nullable()->change();
            $table->time('check_out')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Restore valid time values before reverting the columns to NOT NULL.
        DB::table('attendance')->whereNull('check_in')->update(['check_in' => '00:00:00']);
        DB::table('attendance')->whereNull('check_out')->update(['check_out' => '00:00:00']);

        if (Schema::hasColumn('attendance', 'status')) {
            Schema::table('attendance', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        Schema::table('attendance', function (Blueprint $table) {
            $table->time('check_in')->nullable(false)->change();
            $table->time('check_out')->nullable(false)->change();
        });
    }
};
