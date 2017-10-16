# Mangapie

This is a self-hosted server for archived manga.  
No, this is not a manga downloader for 3rd party sites; nor will it ever be.

## Requirements
* PHP
* Any DBMS that Laravel supports
* Composer

## Installation & Configuration
Check out the wiki.

## Features
* Support for
	- zip/cbz
	- rar/cbr
* Reader
    - Automatic transition from one archive to another
    - Image preloading
* Mangaupdates scraper
	- Description, Type, Associated Names, Genres, Authors, Artists, Year
	- Uses Jaro-Winkler for name matching   

## Screenshots
![Manga](/screenshots/manga.png?raw=true "Manga")
![Reader1](/screenshots/reader-1.png?raw=true "Reader1")
