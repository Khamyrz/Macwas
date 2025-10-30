<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->renameColumn('successful', 'success');
        });
    }

    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->renameColumn('success', 'successful');
        });
    }
};
