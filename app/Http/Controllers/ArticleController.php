<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Utilities\bigram; //２文字ずつの文字（トークン）に分けるために使用
use  DB; //SQL文を直接書くために使用

class ArticleController extends Controller
{
    // private $bigram;

    // function __construct(){
    //     $this->bigram = new Bigram();
    // }
    //バリデーション省略
    function store(Request $request){
        $article = Article::Create([
            'title' => $request->title,
            'body' => $request->body
        ]);

        $bigram = new Bigram();
        $title = $bigram->convert_to_bigram($request->title);
        $body = $bigram->convert_to_bigram($request->body);

        // rowid:暗黙的にVIRTUAL TABLEに自動で作成されるidカラム（ここにarticlesテーブルのprimary keyの値を格納することで紐づける）
        DB::insert('insert into articles_fts (rowid, title, body) values (?, ?, ?)', [$article->id, $title, $body]);
        return response()->json(["title" => $title, "body" => $body]);
    }


    function getArticles(Request $request){        
        // dd("test");
        // SELECT * FROM hoge h INNER JOIN hoge_fts hf ON h.id = hf.docid WHERE words MATCH "ho og ge"
        // $result = DB::select('select * from articles INNER JOIN articles_fts ON articles.id = articles_fts_content.docid where  articles_fts MATCH ?', ['人殺し']);


        // OKなクエリ
        // $result = DB::select('select * from articles_fts where articles_fts.rowid = 2');

        $bigram = new Bigram();
        $keyword = $bigram->convert_to_bigram($request->keyword, true);
        // dd($keyword);
        // ORDER BY rankでよりマッチしているものが
        $result = DB::select('select articles.* from articles INNER JOIN articles_fts ON articles.id = articles_fts.rowid where articles_fts MATCH ? ORDER BY rank', [$keyword]);
        return $result;
    }
}
