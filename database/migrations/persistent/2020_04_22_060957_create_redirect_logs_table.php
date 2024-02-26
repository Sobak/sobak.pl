<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedirectLogsTable extends Migration
{
    public function up()
    {
        // Not using a relation here is intentional as the database containing redirects
        // is recreated on every content change, IDs are not guaranteed to be constant
        // and the redirects themselves can come and go.
        Schema::create('redirect_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_url')->index();
            $table->string('target_url');
            $table->string('ip');
            $table->text('user_agent');
            $table->timestamp('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('redirect_logs');
    }
}
