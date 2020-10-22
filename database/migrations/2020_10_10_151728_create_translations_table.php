<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-translate.table'), function (Blueprint $table) {
            $table->string('id', 50)->unique()->primary();
            $table->text('text_original');
            $table->text('text_translated')->nullable();
            $table->string('lang_from');
            $table->string('lang_to');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel-translate.table'));
    }
}
