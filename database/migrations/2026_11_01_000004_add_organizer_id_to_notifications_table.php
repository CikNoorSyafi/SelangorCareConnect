<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notifications', 'organizer_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->foreignId('organizer_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        // Backfill campaign-linked notifications using the campaign owner.
        // This preserves visibility of existing organizer campaign notices.
        if (Schema::hasColumn('campaigns', 'organizer_id')) {
            DB::table('notifications')
                ->join('campaigns', 'notifications.campaign_id', '=', 'campaigns.id')
                ->whereNull('notifications.organizer_id')
                ->whereNotNull('campaigns.organizer_id')
                ->update([
                    'notifications.organizer_id' => DB::raw('campaigns.organizer_id'),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notifications', 'organizer_id')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropForeign(['organizer_id']);
                $table->dropColumn('organizer_id');
            });
        }
    }
};
