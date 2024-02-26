<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferrerColumnToRedirectLogs extends Migration
{
    public function up(): void
    {
        Schema::table('redirect_logs', function (Blueprint $table) {
            $table->string('referrer')->nullable()->after('target_url');
        });
    }

    public function down(): void
    {
        Schema::table('redirect_logs', function (Blueprint $table) {
            $table->dropColumn('referrer');
        });
    }
}
