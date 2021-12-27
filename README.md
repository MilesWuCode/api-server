# Api-Server

-   [Detail](document/README.md)
-   [Postman File](document)
## Packages

-   laravel 8
-   laravel/passport
-   nuwave/lighthouse
-   beyondcode/laravel-query-detector
-   spatie/laravel-tags
-   spatie/laravel-fractal
-   spatie/laravel-medialibrary
-   cybercog/laravel-love
-   sentry/sentry-laravel

## Run

```sh
cp .env.sail .env

sail php artisan k:g

sail up -d

sail php artisan o:c

sail php artisan migrate

sail php artisan passport:install

sail php artisan love:reaction-type-add --default

# Testing
sail php artisan db:seed --class=TestingSeeder

sail composer dump-autoload
```
