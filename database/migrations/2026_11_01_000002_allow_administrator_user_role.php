<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The administrator account is provisioned manually and is never offered
     * by the public registration form.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Convert the original three-value enum to a string so the
            // administrator role can be stored without changing public roles.
            $table->string('role', 30)->change();
        });
    }

    public function down(): void
    {
        // Intentionally left irreversible: reverting to the original enum
        // would fail or discard data while administrator accounts still exist.
    }
};
