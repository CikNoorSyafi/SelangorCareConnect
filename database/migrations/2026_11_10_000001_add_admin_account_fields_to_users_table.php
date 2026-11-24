<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('password');
            });
        }

        if (!Schema::hasColumn('users', 'verification_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('verification_status', 20)->nullable()->after('organization');
            });
        }

        if (!Schema::hasColumn('users', 'verification_note')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('verification_note', 500)->nullable()->after('verification_status');
            });
        }

        DB::table('users')
            ->where('role', 'organizer')
            ->whereNull('verification_status')
            ->update(['verification_status' => 'Pending']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'verification_note')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('verification_note');
            });
        }
        if (Schema::hasColumn('users', 'verification_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('verification_status');
            });
        }
        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
