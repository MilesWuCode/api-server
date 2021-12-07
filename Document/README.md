# Laravel + Laravel Passport + Lighthouse

## Plan

-   medialibrary

## Install Laravel, Sail

```sh
# Laravel, install
curl -s "https://laravel.build/graphql-server" | bash

# Laravel Sail, run docker
./vendor/bin/sail up

# Laravel Sail, alias sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

## Lighthouse

```sh
# install package
sail composer require nuwave/lighthouse

# GraphQL DevTools
sail composer require mll-lab/laravel-graphql-playground

# Publish the default schema
sail php artisan vendor:publish --tag=lighthouse-schema

# Configuration : config/lighthouse.php
sail php artisan vendor:publish --tag=lighthouse-config
```

config/cors.php

```diff
return [
-   'paths' => ['api/*', 'sanctum/csrf-cookie'],
+   'paths' => ['api/*', 'graphql', 'sanctum/csrf-cookie'],
    ...
];
```

## Laravel Password

```sh
# install package
sail composer require laravel/passport

# oauth private/public key file
sail php artisan passport:install

# publish config
sail php artisan vendor:publish --tag=passport-config
```

app/Models/User.php

```diff
- use Laravel\Sanctum\HasApiTokens;
+ use Laravel\Passport\HasApiTokens;
```

app/Providers/AuthServiceProvider.php

```diff
+ use Laravel\Passport\Passport;

    public function boot()
    {
        $this->registerPolicies();

-       //
+       if (! $this->app->routesAreCached()) {
+           Passport::routes();
+       }
    }
```

config/auth.php

```diff
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
+       'api' => [
+           'driver' => 'passport',
+           'provider' => 'users',
+       ],
    ],
```

## Test

```sh
# Tinker
sail php artisan tinker

# run test
sail php artisan test
```

```php
# Create User
$user = new App\Models\User();
$user->name = 'Miles';
$user->email = 'miles@email.com';
$user->password = Hash::make('password');
$user->save();
```

```json
{ "Authorization": "Bearer {token}" }
```

## Page

-   http://localhost
-   http://localhost/graphql-playground

## API

-   http://localhost/graphql
-   http://localhost/oauth/token
-   http://localhost/api/user

## Test

```sh
# 可以直接使用.env.testing來執行測試
cp .env .env.testing

#
sail php artisan test --testsuite=Feature --stop-on-failure
```

## .zshrc

```sh
# alias sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# switch php version
export PATH="/usr/local/opt/php@7.4/bin:$PATH"
export PATH="/usr/local/opt/php@7.4/sbin:$PATH"
# export PATH="/usr/local/opt/php@8.0/bin:$PATH"
# export PATH="/usr/local/opt/php@8.0/sbin:$PATH"
```

## Comment

```sh
sail php artisan make:model Todo -a --api --test
```

## N+1 check

```sh
# install
sail composer require beyondcode/laravel-query-detector --dev

# provider
sail php artisan vendor:publish --provider="BeyondCode\QueryDetector\QueryDetectorServiceProvider"
```

## Tags

```sh
# install
sail composer require spatie/laravel-tags

# migration
sail php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-migrations"

# migrate
sail php artisan migrate

# config
sail php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-config"
```

## 
```sh
# install
sail composer require spatie/laravel-fractal

# config
php artisan vendor:publish --provider="Spatie\Fractal\FractalServiceProvider"
```
