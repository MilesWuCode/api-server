<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Passport
        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }

        // 更改驗證信方法2
        if (false) {
            VerifyEmail::createUrlUsing(function ($notifiable) {
                $query = http_build_query([
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                    'expires' => Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))->getTimestamp(),
                ]);

                return env('FRONTEND_EMAIL_VERIFY_URL') . '?' . $query;
            });
        }

        // 更改驗證信方法3
        if (false) {
            VerifyEmail::createUrlUsing(function ($notifiable) {
                $verifyUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                    [
                        'id' => $notifiable->getKey(),
                        'hash' => sha1($notifiable->getEmailForVerification()),
                    ]
                );

                return env('FRONTEND_EMAIL_VERIFY_URL') . '?verify_url=' . urlencode($verifyUrl);
            });
        }
    }
}
