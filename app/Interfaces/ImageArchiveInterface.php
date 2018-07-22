<?php

namespace App\Interfaces;

use App\ImageArchiveZip;
use App\ImageArchiveRar;
use App\IntlString;

interface ImageArchiveInterface
{
    public function good();
    public function getInfo($index);
    public function getImage($index, &$size);
    public function getImageUrlPath($index, &$size);
    public function getImages();
    public function extract($index, $path, $name);
}
