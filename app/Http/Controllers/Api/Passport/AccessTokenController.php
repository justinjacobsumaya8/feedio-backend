<?php

namespace App\Http\Controllers\Api\Passport;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Http\Controllers\AccessTokenController as AuthController;

use App\Services\AuthenticatedUserService;

class AccessTokenController extends AuthController
{
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            $data = json_decode(parent::issueToken($request)->content(), true);

            $user = $user = User::select(
                "id",
                "name",
                "email",
            )
            ->where('email', '=', $request->getParsedBody()['username'])
            ->firstOrFail()
            ->toArray();

            return response()->json(array_merge(["user" => $user], $data));
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
