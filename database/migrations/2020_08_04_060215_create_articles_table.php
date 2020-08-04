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
        DB::statement('CREATE VIRTUAL TABLE articles_fts USING fts5( title, body )');

        // DB::statement('CREATE VIRTUAL TABLE articles_fts USING fts4(title, body )');

        // DB::statement('CREATE VIRTUAL TABLE articles_fts USING fts4(
        //     id" integer not null primary key autoincrement
        //     title VARCHAR(256) NOT NULL,
        //     body TEXT
        //   );');

        //   CREATE TABLE "articles" ("id" integer not null primary key autoincrement, "title" varchar not null, "body" text not null, "created_at" datetime null, "updated_at" datetime null)
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
