<?php

namespace App\Interfaces;

interface WatchDescriptorInterface
{
    function getWd();
    function hasParent();
    function getParent();
    function getPath();
    function getData();
    function isDirectory();
    function isSymbolicLink();
}
