<?php

namespace App\Interfaces;

interface EditableInterface
{
    public function deleteType();
    public function deleteDescription();
    public function deleteAssociatedName($name);
    public function deleteAuthorReference($authorName);
    public function deleteArtistReference($artistName);
    public function deleteGenreReference($genreName);
    public function deleteYear();

    public function setType($type);
    public function setDescription($description);
    public function addAssociatedName($name);
    public function addAssociatedNames($names);
    public function addAuthor($authorName);
    public function addAuthors($authorNames);
    public function addArtist($artistName);
    public function addArtists($artistNames);
    public function addGenre($genreName);
    public function addGenres($genreNames);
    public function setYear($year);
}
