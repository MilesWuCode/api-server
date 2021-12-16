# Laravel + Laravel Passport + Lighthouse

## Install Laravel, Sail

```sh
# Laravel, install
curl -s "https://laravel.build/api-server" | bash

# Laravel Sail, run docker
./vendor/bin/sail up -d

# Laravel Sail, alias sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

## nuwave/lighthouse

```sh
# install package
composer require nuwave/lighthouse

# GraphQL DevTools
composer require mll-lab/laravel-graphql-playground

# Publish the default schema
php artisan vendor:publish --tag=lighthouse-schema

# Configuration : config/lighthouse.php
php artisan vendor:publish --tag=lighthouse-config
```

config/cors.php

```diff
return [
-   'paths' => ['api/*', 'sanctum/csrf-cookie'],
+   'paths' => ['api/*', 'graphql', 'sanctum/csrf-cookie'],
    ...
];
```

## laravel/passport

```sh
# install package
composer require laravel/passport

# oauth private/public key file
php artisan passport:install

# publish config
php artisan vendor:publish --tag=passport-config
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
php artisan tinker

# run test
php artisan test --testsuite=Feature --stop-on-failure

# 可以直接使用.env.testing來執行測試
cp .env .env.testing
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

## .zshrc

```sh
# alias sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# switch php version
export PATH="/opt/homebrew/opt/php@7.4/bin:$PATH"
export PATH="/opt/homebrew/opt/php@7.4/sbin:$PATH"
# export PATH="/opt/homebrew/opt/php@8.1/bin:$PATH"
# export PATH="/opt/homebrew/opt/php@8.1/sbin:$PATH"
```

## Todo

```sh
php artisan make:model Todo -a --api --test
```

## N+1 check

```sh
# install
composer require beyondcode/laravel-query-detector --dev

# provider
php artisan vendor:publish --provider="BeyondCode\QueryDetector\QueryDetectorServiceProvider"
```

## laravel-tags

```sh
# install
composer require spatie/laravel-tags

# migration
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-migrations"

# migrate
php artisan migrate

# config
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="tags-config"
```

## laravel-fractal

```sh
# install
composer require spatie/laravel-fractal

# config
php artisan vendor:publish --provider="Spatie\Fractal\FractalServiceProvider"

# make
php artisan make:transformer UserTransformer
```

## laravel-medialibrary

```sh
# install
composer require spatie/laravel-medialibrary

# migration
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"

# migrate
php artisan migrate

# config
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```

```php
# config/filesystems.php
'media' => [
    'driver' => 'local',
    'root'   => public_path('media'),
    'url'    => env('APP_URL').'/media',
],
```

```ini
# .env
# laravel-medialibrary
MEDIA_DISK=media
```

## laravelista/comments(wip)

```sh
composer require laravelista/comments
```

## cybercog/laravel-love(wip)

```sh
#
composer require cybercog/laravel-love

#
php artisan migrate

#
php artisan love:reaction-type-add --default

#
sail php artisan love:setup-reacterable --model="App\Models\User" --nullable
sail php artisan migrate
sail php artisan love:register-reacters --model="App\Models\User"

#
sail php artisan love:setup-reactable --model="App\Models\Blog" --nullable
sail php artisan migrate
sail php artisan love:register-reactants --model="App\Models\Blog"
```

## sentry/sentry-laravel(wip)

```sh
composer require sentry/sentry-laravel
```

## spatie/laravel-responsecache(wip)

```sh
composer require spatie/laravel-responsecache
```

## laravel/scout(wip)

```sh
composer require laravel/scout
```

## spatie/eloquent-sortable(wip)

```sh
composer require spatie/eloquent-sortable
```

## swooletw/laravel-swoole(wip)

## wip

-   user avatar, default value
-   user update
-   blog update with images
-   verify code
-   graphql/api thumb
-   file upload

## run server

```sh
php artisan o:c
php artisan migrate
php artisan passport:install
php artisan love:reaction-type-add --default
php artisan db:seed --class=TestingSeeder
composer dump-autoload
```
