<?php

namespace App\Http\Controllers;

use App\Http\Requests\SocialiteRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function singin(SocialiteRequest $request): JsonResponse
    {
        $driver = $request->input('driver');

        $token = $request->input('token');

        $socialiteUser = Socialite::driver($driver)->stateless()->userFromToken($token);

        $user = User::where('email', $socialiteUser->email)->first();

        if ($user) {
            // $user->update([
            //     'github_token' => $socialiteUser->token,
            //     'github_refresh_token' => $socialiteUser->refreshToken,
            // ]);
        } else {
            $user = User::create([
                'name' => $socialiteUser->name,
                'email' => $socialiteUser->email,
                'password' => '',
                // 'github_id' => $socialiteUser->id,
                // 'github_token' => $socialiteUser->token,
                // 'github_refresh_token' => $socialiteUser->refreshToken,
            ]);

            $user->markEmailAsVerified();
        }

        $token = $user->createToken('Laravel Password Grant Client');

        $accessToken = $token->accessToken;

        $expires_at = $token->token->expires_at->diffInSeconds(Carbon::now());

        return response()->json([
            'access_token' => $accessToken,
            'expires_in' => $expires_at,
            'token_type' => 'Bearer',
        ], 200);
    }
}
