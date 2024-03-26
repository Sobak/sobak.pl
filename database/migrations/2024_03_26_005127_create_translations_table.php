<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('canonical_slug');
            $table->string('translated_slug');
            $table->char('language', 5);
            $table->string('type', 40);

            $table->unique(['language', 'type', 'canonical_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
}
