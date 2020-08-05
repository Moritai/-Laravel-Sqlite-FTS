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
        // トークン分割
        $bigram = new Bigram();
        $keyword = $bigram->convert_to_bigram($request->keyword, true);

        // ORDER BY rankでよりマッチしているものが上にくるようにする。
        $result = DB::select('select articles.* from articles INNER JOIN articles_fts ON articles.id = articles_fts.rowid where articles_fts MATCH ? ORDER BY rank', [$keyword]);
        
        //ページネーション機能を付与 
        $result = $this->arrayPaginator($result, $request);
        return $result;
    }

    function arrayPaginator($array, $request){
        // リクエストのbodyにpageキーがあればその値を返し、なければ１を返す。
        $page = $request->input('page', 1);
        // 一ページが持つデータ数
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        // ページネーションを実行
        return new \Illuminate\Pagination\LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
}
