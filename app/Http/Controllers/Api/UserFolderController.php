<?php

namespace App\Http\Controllers\Api;

use App\Events\UserFolderSubscriptionEvent;
use App\Http\Controllers\Controller;
use App\Services\HelperService;
use App\Services\UserFolderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserFolderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user('api');
        $userFolders = (new UserFolderService())->getFolders($user->id);

        return response()->json([
            "data" => $userFolders,
            "user" => $user
        ]);
    }

    public function create(Request $request)
    {
        $errors = (new HelperService())->validate($request, [
            "title" => ["required", "string", "max:255"],
        ]);

        if (count($errors)) {
            return response([
                "message" => $errors
            ], 422);
        }

        $userFolder = (new UserFolderService())->create($request);

        return response()->json([
            "data" => $userFolder
        ]);
    }

    public function show(Request $request, $id)
    {
        $userFolder = (new UserFolderService())->show($id);

        return response()->json([
            'data' => $userFolder
        ]);
    }

    public function update(Request $request, $id)
    {
        $errors = (new HelperService())->validate($request, [
            "title" => ["required", "string", "max:255"],
        ]);

        if (count($errors)) {
            return response([
                "message" => $errors
            ], 422);
        }

        $userFolder = (new UserFolderService())->find($id);
        $userFolder->title = $request->title;
        $userFolder->save();

        return response()->json([
            'data' => $userFolder
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $userFolder = (new UserFolderService())->find($id);
        $userFolder->delete();

        return response()->json([
            'data' => 1
        ]);
    }

    public function allSubscriptions(Request $request)
    {
        $user = $request->user('api');
        $userFolderSubscriptions = (new UserFolderService())->getAllSubscriptions($user->id);

        return response()->json([
            'data' => $userFolderSubscriptions
        ]);
    }

    public function createSubscription(Request $request)
    {
        $errors = (new HelperService())->validate($request, [
            "source_id" => "required_without_all:category_id,author_id",
            "category_id" => "required_without_all:source_id,author_id",
            "author_id" => "required_without_all:source_id,category_id",
            "action" => "required|in:existing_folder,new_folder",
            "title" => Rule::requiredIf(function () use ($request) {
                return $request->action == "new_folder";
            }),
            "user_folder_id" => Rule::requiredIf(function () use ($request) {
                return $request->action == "existing_folder";
            }),
        ]);

        if (count($errors)) {
            return response([
                "message" => $errors
            ], 422);
        }

        $userFolderService = new UserFolderService();

        // Create new folder
        if ($request->action === "new_folder") {
            $userFolder = $userFolderService->create($request);
        }
        // Existing folder
        else {
            $userFolder = $userFolderService->find($request->user_folder_id);
        }

        $userFolderSubscription = $userFolderService->createSubscription($request, $userFolder->id);

        // Populate user feed
        event(new UserFolderSubscriptionEvent($userFolderSubscription->id));

        return response()->json([
            "data" => $userFolderSubscription
        ]);
    }

    public function unsubscribe(Request $request, $userFolderSubscriptionId)
    {
        $userFolderSubscription = (new UserFolderService())->findSubscription($userFolderSubscriptionId);
        $userFolderSubscription->userFeeds()->delete();

        $userFolderSubscription->delete();

        return response()->json([
            "data" => "success"
        ]);
    }
}
