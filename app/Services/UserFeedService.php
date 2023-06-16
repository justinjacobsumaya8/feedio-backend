<?php

namespace App\Services;

use App\Models\UserFeed;
use Illuminate\Http\Request;

class UserFeedService
{
    public function query(Request $request, int $userId)
    {
        $userFeed = UserFeed::select(
            'user_feeds.*',
            'user_folder_subscriptions.source_id',
            'user_folder_subscriptions.category_id',
            'user_folder_subscriptions.author_id',
            'sources.name AS source_name',
            'categories.name AS category_name',
            'authors.name AS author_name',
        )
            ->leftJoin('user_folder_subscriptions', 'user_folder_subscriptions.id', 'user_feeds.user_folder_subscription_id')
            ->leftJoin('sources', 'sources.id', 'user_folder_subscriptions.source_id')
            ->leftJoin('categories', 'categories.id', 'user_folder_subscriptions.category_id')
            ->leftJoin('authors', 'authors.id', 'user_folder_subscriptions.author_id')
            ->where('user_id', $userId)
            ->with(['article' => function ($articleQuery) {
                $articleQuery->generalQuery();
            }]);

        if ($request->show_feed_by === "category") {
            $userFeed = $userFeed->whereNotNull("user_folder_subscriptions.category_id");
        } else if ($request->show_feed_by === "author") {
            $userFeed = $userFeed->whereNotNull("user_folder_subscriptions.author_id");
        } else {
            $userFeed = $userFeed->whereNotNull("user_folder_subscriptions.source_id");
        }

        return $userFeed;
    }
}
