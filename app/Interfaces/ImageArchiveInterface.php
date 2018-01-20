<?php

namespace App\Interfaces;

use App\ImageArchiveZip;
use App\ImageArchiveRar;
use App\IntlString;

interface ImageArchiveInterface
{
    public function good();
    public function getInfo($index);
    public function getContents($index, &$size);
    public function getImages();
}
