<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'age')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedTinyInteger('age')->nullable()->after('phone');
            });
        }

        if (!Schema::hasColumn('users', 'volunteer_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('volunteer_status', ['Active', 'Inactive'])->nullable()->after('age');
            });
        }

        if (!Schema::hasColumn('users', 'notes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('volunteer_status');
            });
        }

        DB::table('users')
            ->where('role', 'volunteer')
            ->whereNull('volunteer_status')
            ->update(['volunteer_status' => 'Active']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'notes')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notes');
            });
        }

        if (Schema::hasColumn('users', 'volunteer_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('volunteer_status');
            });
        }

        if (Schema::hasColumn('users', 'age')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('age');
            });
        }
    }
};
