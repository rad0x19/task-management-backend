# TASK MANAGEMENT[Backend] 
# By Roldhan A. Dasalla


## Installation
```sh
composer install
cp .env.example .env
```
- update `.env` database credentials.
```sh
php artisan migrate
php artisan storage:link
# optional (if you`re not going to use a laravel valet, use the following command)
php artisan server
```