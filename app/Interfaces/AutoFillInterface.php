<?php

namespace App\Interfaces;

interface AutoFillInterface
{
    public static function autofill($manga);
    public static function autofillFromId($manga, $id);
}
