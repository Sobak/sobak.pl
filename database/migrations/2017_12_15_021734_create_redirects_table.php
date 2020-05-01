<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedirectsTable extends Migration
{
    public function up()
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source_url')->unique();
            $table->string('target_url');
            $table->integer('http_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('redirects');
    }
}
