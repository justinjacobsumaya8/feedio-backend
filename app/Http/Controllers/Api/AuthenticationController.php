<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function logout(Request $request)
    {
        $user = $request->user('api');
        if ($user) {
            $user->token()->revoke();

            return ["message" => "Logged out"];
        }

        throw new Exception("Unauthorized", 401);
    }
}
