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

#### Setup your web server (Example config for nginx with php 7.0)
```
server {
	listen 127.0.0.1:80;
	#listen 192.168.1.142:80; 
	server_name localhost;

	location /mangapie/ {
		proxy_pass http://127.0.0.1:8000/;
	}
}

server {
	listen 127.0.0.1:8000;
	server_name mangapie;
	root /home/pierobot/github/mangapie/;
	access_log /home/pierobot/github/mangapie/nginx-log/access.log;
	error_log /home/pierobot/github/mangapie/nginx-log/error.log;

	index index.php;

	location / {
		try_files $uri $uri/ /index.php$args;
	}

	location ~ \.php$ {
		try_files $uri =404;

		include fastcgi.conf;
		fastcgi_index index.php;
		fastcgi_pass unix:/run/php/php7.0-fpm.sock;
	}
}

```

#### Login using 'dev' for both username and password. Change username and password and add libraries.