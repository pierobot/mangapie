# Mangapie

This is a self-hosted server for archived manga.

## Features
* Support for
	- zip/cbz
	- rar/cbr
* Reader
    - Automatic transition from one archive to another
    - Image preloading
    - Option to read left to right or right to left
    - Saves archive progress
        - Complete, incomplete, unread
        - Date last read
* Search
    - Autocomplete for quick and dirty basic searches
    - Advanced search
        - By genres
        - By author and/or artist
        - By keywords
            - Also matches against associated names
* Metadata
    - Mangaupdates
        - Description, type, associated names, genres, authors, artists, year
        - Uses Jaro-Winkler for name matching
    - Editable
        - Description, type, associated names, genres, authors, artists, year
        - Select covers from archives

## Requirements
* PHP
* Any DBMS that Laravel supports
* Composer

## Installation & Configuration
Check out the wiki.

## Screenshots
![Manga](/screenshots/index.png?raw=true "Index")
![Reader1](/screenshots/reader-01.png?raw=true "Reader1")
