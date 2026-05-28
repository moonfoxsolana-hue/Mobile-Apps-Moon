<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index');
    }

    public function list()
    {
        $news = News::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($news);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'required|url',
        ]);

        News::create($request->only('title', 'content', 'image'));

        return response()->json(['message' => 'Berita berhasil ditambahkan']);
    }
}
