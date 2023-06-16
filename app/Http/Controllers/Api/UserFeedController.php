<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultResource;
use App\Services\UserFeedService;
use Illuminate\Http\Request;

class UserFeedController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user('api');
        $feed = (new UserFeedService())->query($request, $user->id)->paginate(20);

        return DefaultResource::collection($feed);
    }
}
