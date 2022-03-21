<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class CustomVerifyEmail extends VerifyEmail
{
    // 更改驗證信方法1
    // vendor/laravel/framework/src/Illuminate/Auth/Notifications/VerifyEmail.php
    protected function verificationUrl($notifiable)
    {
        $verify = $notifiable->verifies()->create();

        $query = http_build_query([
            // laravel既有id,hash,expires
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
            'expires' => Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->getTimestamp(),
            // 自定義
            'code' => $verify->code,
        ]);

        return env('FRONTEND_EMAIL_VERIFY_URL') . '?' . $query;
    }
}
