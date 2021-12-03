<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class CustomVerifyEmail extends VerifyEmail
{
    // 更改驗證信方法1
    protected function verificationUrl($notifiable)
    {
        $query = http_build_query([
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
            'expires' => Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->getTimestamp(),
        ]);

        return env('FRONTEND_EMAIL_VERIFY_URL') . '?' . $query;
    }
}
