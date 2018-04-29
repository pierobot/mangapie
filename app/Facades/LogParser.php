<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LogParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LogParser';
    }
}