# Laravel + Laravel Passport + Lighthouse

## History

```sh
# Laravel, install
curl -s "https://laravel.build/graphql-server" | bash

# Laravel Sail, run docker
./vendor/bin/sail up

# Laravel Sail, alias sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# Lighthouse, install graphql server package
sail composer require nuwave/lighthouse

# Lighthouse, Publish the default schema
sail php artisan vendor:publish --tag=lighthouse-schema

# Lighthouse, Install GraphQL DevTools
sail composer require mll-lab/laravel-graphql-playground

# Lighthouse, Configuration : config/lighthouse.php
sail php artisan vendor:publish --tag=lighthouse-config

# Database, Running Migrations
sail php artisan migrate
```

config/cors.php

```diff
return [
-   'paths' => ['api/*', 'sanctum/csrf-cookie'],
+   'paths' => ['api/*', 'graphql', 'sanctum/csrf-cookie'],
    ...
];
```

```sh
# Laravel Passport, install
sail composer require laravel/passport
sail php artisan passport:install
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

```sh
# publish config
sail php artisan vendor:publish --tag=passport-config
```

```sh
# Tinker
sail php artisan tinker
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

.env
```conf
# MailHog
MAIL_FROM_ADDRESS=app@mail.com
```

## Page

-   http://localhost
-   http://localhost/graphql-playground

## API

-   http://localhost/graphql
-   http://localhost/oauth/token
-   http://localhost/api/user
