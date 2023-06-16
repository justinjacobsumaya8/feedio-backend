<?php

namespace App\Services;

use App\Models\UserFolder;
use App\Models\UserFolderSubscription;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserFolderService
{
    public function getFolders($userId)
    {
        $userFolders = UserFolder::with(['userFolderSubscriptions' => function ($q) {
            $q->withCount('userFeeds');
        }])
            ->where('user_id', $userId)
            ->get();

        return $userFolders;
    }

    protected function checkTitleExist(Request $request, $user)
    {
        $userFolder = UserFolder::where('title', $request->title)->where('user_id', $user->id)->first();
        if ($userFolder) {
            throw new Exception("A folder with that name already exist.");
        }
    }

    public function create(Request $request)
    {
        $user = $request->user('api');

        $this->checkTitleExist($request, $user);

        $userFolder = new UserFolder();
        $userFolder->title = $request->title;
        $userFolder->user_id = $user->id;
        $userFolder->save();

        return $userFolder;
    }

    public function find($userFolderId)
    {
        $userFolder = UserFolder::find($userFolderId);
        if (!$userFolder) {
            throw new Exception("User folder doesn't exist.");
        }
        return $userFolder;
    }

    public function findSubscription($userFolderSubscriptionId)
    {
        $userFolderSubscription = UserFolderSubscription::with('userFeeds')->find($userFolderSubscriptionId);
        if (!$userFolderSubscription) {
            throw new Exception("User folder subscription doesn't exist.");
        }
        return $userFolderSubscription;
    }

    public function createSubscription(Request $request, $userFolderId)
    {
        $userFolderSubscription = UserFolderSubscription::where("user_folder_id", $userFolderId);

        if ($request->source_id) {
            $userFolderSubscription = $userFolderSubscription->where("source_id", $request->source_id);
        }

        if ($request->category_id) {
            $userFolderSubscription = $userFolderSubscription->where("category_id", $request->category_id);
        }

        if ($request->author_id) {
            $userFolderSubscription = $userFolderSubscription->where("author_id", $request->author_id);
        }

        $userFolderSubscription = $userFolderSubscription->first();

        if (!$userFolderSubscription) {
            $userFolderSubscription = new UserFolderSubscription();
            $userFolderSubscription->user_folder_id = $userFolderId;
            $userFolderSubscription->source_id = $request->source_id;

            if ($request->source_id) {
                $userFolderSubscription->source_id = $request->source_id;
            }

            if ($request->category_id) {
                $userFolderSubscription->category_id = $request->category_id;
            }

            if ($request->author_id) {
                $userFolderSubscription->author_id = $request->author_id;
            }

            $userFolderSubscription->save();
        }

        return $userFolderSubscription;
    }

    public function show($id)
    {
        $userFolder = UserFolder::with(['userFolderSubscriptions' => function ($q) {
            $q->select(
                'user_folder_subscriptions.*',
                'sources.name AS source_name',
                'categories.name AS category_name',
                'authors.name AS author_name',
            )
                ->leftJoin('sources', 'sources.id', 'user_folder_subscriptions.source_id')
                ->leftJoin('categories', 'categories.id', 'user_folder_subscriptions.category_id')
                ->leftJoin('authors', 'authors.id', 'user_folder_subscriptions.author_id')
                ->with(['userFeeds.article' => function ($articleQuery) {
                    $articleQuery->generalQuery();
                }]);
        }])
            ->find($id);

        if (!$userFolder) {
            throw new Exception("User folder doesn't exist");
        }

        return $userFolder;
    }

    public function getAllSubscriptions(int $userId)
    {
        return UserFolderSubscription::select(
            'user_folder_subscriptions.*',
            'user_folders.user_id',
            'sources.name AS source_name',
            'categories.name AS category_name',
            'authors.name AS author_name',
        )
            ->leftJoin('user_folders', 'user_folders.id', 'user_folder_subscriptions.user_folder_id')
            ->leftJoin('sources', 'sources.id', 'user_folder_subscriptions.source_id')
            ->leftJoin('categories', 'categories.id', 'user_folder_subscriptions.category_id')
            ->leftJoin('authors', 'authors.id', 'user_folder_subscriptions.author_id')
            ->with(['userFeeds.article' => function ($articleQuery) {
                $articleQuery->generalQuery();
            }])
            ->where('user_folders.user_id', $userId)
            ->get();
    }
}
