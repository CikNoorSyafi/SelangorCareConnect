<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'frequency')) {
                $table->string('frequency', 20)->default('one_time')->after('receipt_no');
            }

            if (!Schema::hasColumn('donations', 'receipt_email')) {
                $table->string('receipt_email')->nullable()->after('frequency');
            }

            if (!Schema::hasColumn('donations', 'dedication')) {
                $table->text('dedication')->nullable()->after('receipt_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $columns = [];
            foreach (['dedication', 'receipt_email', 'frequency'] as $column) {
                if (Schema::hasColumn('donations', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
