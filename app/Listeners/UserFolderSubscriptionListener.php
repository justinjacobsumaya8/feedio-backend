<?php

namespace App\Listeners;

use App\Events\UserFolderSubscriptionEvent;
use App\Models\UserFeed;
use App\Models\UserFolderSubscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserFolderSubscriptionListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserFolderSubscriptionEvent $event): void
    {
        $userFolderSubscription = UserFolderSubscription::select('user_folder_subscriptions.*', 'user_folders.user_id')
            ->leftJoin('user_folders', 'user_folders.id', 'user_folder_subscriptions.user_folder_id')
            ->with('sourceArticles', 'categoryArticles', 'authorArticles')
            ->find($event->userFolderSubscriptionId);

        if ($userFolderSubscription) {
            if ($userFolderSubscription->source_id) {
                foreach ($userFolderSubscription->sourceArticles as $sourceArticle) {
                    UserFeed::firstOrCreate([
                        'user_id' => $userFolderSubscription->user_id,
                        'article_id' => $sourceArticle->id,
                        'user_folder_subscription_id' => $userFolderSubscription->id
                    ]);
                }
            }

            if ($userFolderSubscription->category_id) {
                foreach ($userFolderSubscription->categoryArticles as $categoryArticle) {
                    UserFeed::firstOrCreate([
                        'user_id' => $userFolderSubscription->user_id,
                        'article_id' => $categoryArticle->id,
                        'user_folder_subscription_id' => $userFolderSubscription->id
                    ]);
                }
            }

            if ($userFolderSubscription->author_id) {
                foreach ($userFolderSubscription->authorArticles as $authorArticle) {
                    UserFeed::firstOrCreate([
                        'user_id' => $userFolderSubscription->user_id,
                        'article_id' => $authorArticle->article_id,
                        'user_folder_subscription_id' => $userFolderSubscription->id
                    ]);
                }
            }
        }
    }
}
