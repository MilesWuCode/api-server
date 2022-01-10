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

# ide helper
php artisan lighthouse:ide-helper
```

```ini
# .gitignore
_lighthouse_ide_helper.php
programmatic-types.graphql
schema-directives.graphql
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

config/cors.php

```diff
    'paths' => [
        'api/*',
        'graphql',
        'sanctum/csrf-cookie',
+        'oauth/*',
    ],
```

## Test

```sh
# Tinker
php artisan tinker

# run test
php artisan test --testsuite=Feature --stop-on-failure

# run test
sail php artisan test --filter test_create tests/Feature/Models/BlogTest.php

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

## Make Model

```sh
php artisan make:model Todo --all --api --test
```

## barryvdh/laravel-ide-helper

```sh
# install
composer require --dev barryvdh/laravel-ide-helper

#
php artisan ide-helper:generate

#
php artisan ide-helper:models
```

```ini
# .gitignore
_ide_helper.php
```

## beyondcode/laravel-query-detector

-   N+1 check

```sh
# install
composer require beyondcode/laravel-query-detector --dev

# provider
php artisan vendor:publish --provider="BeyondCode\QueryDetector\QueryDetectorServiceProvider"
```

## spatie/laravel-tags

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

## spatie/laravel-fractal

```sh
# install
composer require spatie/laravel-fractal

# config
php artisan vendor:publish --provider="Spatie\Fractal\FractalServiceProvider"

# make
php artisan make:transformer UserTransformer
```

## spatie/laravel-medialibrary

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

## beyondcode/laravel-comments(wip)

```sh
# install
composer require beyondcode/laravel-comments

# migrate
php artisan vendor:publish --provider="BeyondCode\Comments\CommentsServiceProvider" --tag="migrations"

php artisan migrate

# config
php artisan vendor:publish --provider="BeyondCode\Comments\CommentsServiceProvider" --tag="config"
```

## cybercog/laravel-love

```sh
# install
composer require cybercog/laravel-love

# migrate
php artisan migrate

# default: like, dislike
php artisan love:reaction-type-add --default

# set model,migrate
sail php artisan love:setup-reacterable --model="App\Models\User" --nullable
sail php artisan migrate
sail php artisan love:register-reacters --model="App\Models\User"

# set model,migrate
sail php artisan love:setup-reactable --model="App\Models\Blog" --nullable
sail php artisan migrate
sail php artisan love:register-reactants --model="App\Models\Blog"
```

## sentry/sentry-laravel

```sh
composer require sentry/sentry-laravel
```

```diff
# App/Exceptions/Handler.php
+   public function report(Throwable $exception)
+   {
+       if (app()->bound('sentry') && $this->shouldReport($exception)) {
+           app('sentry')->captureException($exception);
+       }
+
+       parent::report($exception);
+   }
```

```sh
php artisan sentry:publish --dsn=xxxx
```

## mll-lab/graphql-php-scalars

```sh
composer require mll-lab/graphql-php-scalars
```

[schema.graphql](../graphql/schema.graphql)

```diff
+   scalar JSON @scalar(class: "MLL\\GraphQLScalars\\JSON")
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

## dev

```sh
php artisan lighthouse:ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models
```
