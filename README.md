# Api-Server

## List

-   laravel passport
-   laravel lighthouse

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
