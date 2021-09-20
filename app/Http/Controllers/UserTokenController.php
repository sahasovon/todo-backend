<?php

namespace App\Http\Controllers;

use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserTokenController extends Controller
{
    /**
     * Generate user token for users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken()
    {
        $userToken = UserToken::create([
            'user_uuid' => Str::orderedUuid()
        ]);

        return response()->json([
            'token' => $userToken->user_uuid
        ])->cookie('x-user-token', $userToken->user_uuid, 60);
    }
}
