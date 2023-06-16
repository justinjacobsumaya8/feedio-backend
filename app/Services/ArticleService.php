<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleService
{
    public function query(Request $request)
    {
        $user = $request->user('api');
        $articles = Article::generalQuery()
            // Only explore articles where it haven't been followed yet
            ->whereDoesntHave('userFeeds', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if ($request->keyword) {
            $articles = $articles->where('title', "LIKE", "%{$request->keyword}%");
        }

        if (
            isset($request->filter['range']['start_date']) &&
            isset($request->filter['range']['end_date']) &&
            $request->filter['range']['start_date'] &&
            $request->filter['range']['end_date']
        ) {
            $articles = $articles->whereBetween("published_at", [$request->filter['range']['start_date'], $request->filter['range']['end_date']]);
        }

        if (isset($request->filter['category_id']) && $request->filter['category_id']) {
            $articles = $articles->where('category_id', $request->filter['category_id']);
        }

        if (isset($request->filter['source_id']) && $request->filter['source_id']) {
            $articles = $articles->where('source_id', $request->filter['source_id']);
        }

        return $articles;
    }
}
