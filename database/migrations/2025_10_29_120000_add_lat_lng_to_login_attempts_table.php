<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('login_attempts', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('login_attempts', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('login_attempts', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('login_attempts', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};


