<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactFormMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('contact_form_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->index();
            $table->string('subject');
            $table->text('content');
            $table->text('error')->nullable();
            $table->string('ip')->index();
            $table->text('user_agent');
            $table->timestamp('created_at')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_form_messages');
    }
}
