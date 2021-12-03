<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * User register.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'comfirm_password' => 'required|same:password',
        ])->validate();

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        return response()->json($user->toArray(), 200);
    }

    /**
     * send verify email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendVerifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ])->validate();

        $email = $request->email;

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'email not found',
                'errors' => [
                    'email' => 'not found',
                ],
            ], 422);
        }

        if ($user->hasVerifiedEmail()) {
            abort(400, 'Email already verified.');
        } else {
            $user->sendEmailVerificationNotification();
        }

        return response()->json($user->toArray(), 200);
    }

    /**
     * verify email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'hash' => 'required',
            'expires' => 'required',
        ])->validate();

        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'message' => 'email not found',
                'errors' => [
                    'email' => 'not found',
                ],
            ], 422);
        }

        if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            abort(401, 'Unauthorized');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return response()->json($user->toArray(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user();
    }

    /**
     * Logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function logout(Request $request)
    {
        return $request->user()->token()->revoke();
    }
}
