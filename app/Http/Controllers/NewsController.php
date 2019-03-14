<?php

namespace App\Http\Controllers;
use App\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function news($page=1)
    {
        $pages = News::count();
        if ($pages%10==0)
        {
            $pages=(int)($pages/10);
        }
        else
        {
            $pages = (int)($pages/10)+1;
        }
        if ($page>$pages)
        {
            $page=$pages;
        }
        if ($page==0)
        {
            $page=1;
        }
        if ($pages==0)
        {
            $pages=1;
        }
        $news = News::orderByDesc('id')->limit(10)->offset(10*($page-1))->get();
        foreach ($news as $new) {
            $new = $new->formatDates();
        }
        return view('news')->with(['news'=>$news,'page'=>$page,'pages'=>$pages]);
    }
}
