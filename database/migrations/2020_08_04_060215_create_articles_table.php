<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->timestamps();
        });

        // CREATE VIRTUAL TABLE hoge_fts USING fts4( words TEXT )
        // FTSテーブルでは値がすべて文字列扱いになってしまうので、実際に使用するときはメインのテーブルと全文検索用のFTSテーブルにわけて使う
        DB::statement('CREATE VIRTUAL TABLE articles_fts USING fts5(title, body)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
