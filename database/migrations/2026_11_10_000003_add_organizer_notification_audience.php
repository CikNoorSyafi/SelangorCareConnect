<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notifications MODIFY audience ENUM('All Volunteers','Campaign Volunteers','All Donors','Campaign Donors','Organizer') NOT NULL");
    }

    public function down(): void
    {
        DB::table('notifications')
            ->where('audience', 'Organizer')
            ->update(['audience' => 'Campaign Volunteers']);

        DB::statement("ALTER TABLE notifications MODIFY audience ENUM('All Volunteers','Campaign Volunteers','All Donors','Campaign Donors') NOT NULL");
    }
};
