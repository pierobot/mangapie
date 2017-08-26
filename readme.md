# Mangapie

This is a self-hosted server for archived manga.

## Requirements

* [Composer](https://getcomposer.org/)
* Whatever [Laravel](https://laravel.com/docs/master/installation) requires.
* Any DBMS that Laravel supports.
* [php-rar](https://github.com/cataphract/php-rar)

## Installation

#### Clone this repository and install
```
git clone https://github.com/pierobot/mangapie && cd mangapie
composer install
```

#### Create a database using your preferred DBMS (Example: MySQL)
```
mysql -u user -p
create database mangapie;
quit
```

#### Replace the APP_URL and DB fields in the .env file with appropriate values
```
vim .env
...
:wq
```

#### Initialize the database
```
php artisan migrate
```

#### Login using 'dev' for both username and password. Change username and password and add libraries.