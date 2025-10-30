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
            $table->text('user_agent')->nullable()->after('ip_address'); // 👈 Add this
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropColumn('user_agent');
        });
    }
};
