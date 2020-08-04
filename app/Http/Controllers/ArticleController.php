<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Utilities\bigram; //２文字ずつの文字（トークン）に分けるために使用
use  DB; //SQL文を直接書くために使用

class ArticleController extends Controller
{
    //バリデーション省略
    function store(Request $request){
        $article = Article::Create([
            'title' => $request->title,
            'body' => $request->body
        ]);

        $bigram = new Bigram();
        $title = $bigram->convert_to_bigram($request->title);
        $body = $bigram->convert_to_bigram($request->body);

        DB::insert('insert into articles_fts (title, body) values (?, ?)', [$title, $body]);
        // Article::Create([
        //     'title' => "戦争における「人殺し」の心理学",
        //     'body' => "本来、人間には、人を殺すことに強烈な抵抗がある。それを兵士として殺戮の場＝戦争に送りだすにはどうするか。元米軍将校による戦慄の研究書。"
        // ]);

        // DB::insert('insert into articles_fts (title, body) values (?, ?)', 
        //     ["戦争 争に にお おけ ける る「 「人 人殺 殺し し」 」の の心 心理 理学 学", $request->body]);
        return response()->json(["title" => $title, "body" => $body]);
    }


    function getArticles(){
        // CREATE TABLE 'articles_fts_content'(id INTEGER PRIMARY KEY, c0, c1)
        
        // dd("test");
        // SELECT * FROM hoge h INNER JOIN hoge_fts hf ON h.id = hf.docid WHERE words MATCH "ho og ge"
        // $result = DB::select('select * from articles INNER JOIN articles_fts ON articles.id = articles_fts_content.docid where  articles_fts MATCH ?', ['人殺し']);

        //OKなクエリ
        // $result = DB::select('select * from articles_fts where articles_fts MATCH ?', ['人殺']);
        // OKなクエリ
        // $result = DB::select('select * from articles_fts_content where articles_fts_content.id = 1');

        // OKなクエリ
        // $result = DB::select('select * from articles_fts where articles_fts.rowid = 2');
        $result = DB::select('select * from articles INNER JOIN articles_fts ON articles.id = articles_fts.rowid where articles_fts MATCH ?', ['殺し']);
        return $result;
    }
}
