<?php

namespace App;

use \App\ImageArchiveZip;
use \App\ImageArchiveRar;
use \App\IntlString;

interface ImageArchiveInterface
{
    public function good();
    public function getInfo($index);
    public function getContents($index, &$size);
    public function getImages();
}

class ImageArchive
{
    private static function getExtension($name) {

        $extension_start = mb_strrpos($name, '.');
        if ($extension_start > 0) {
            return mb_strtolower(substr($name, $extension_start + 1));
        }

        return false;
    }

    private static function isJunk($name) {

        $all_junk = [
          '__MACOSX',
          '.DS_STORE'
        ];

        foreach ($all_junk as $junk) {

            $result = IntlString::strncmp(
                        IntlString::convert($name),
                        IntlString::convert($junk),
                        IntlString::strlen($junk));

            if ($result == 0)
                return true;
        }

        return false;
    }

    public static function isImage($name) {

        // there are some images in junk folders that we don't care about
        if (ImageArchive::isJunk($name) === true) {

            return false;
        }

        $image_extensions = [
            'jpg',
            'jpeg',
            'png',
            'gif' // really rare but I've come across this.
        ];

        $found = false;
        $name_extension = ImageArchive::getExtension($name);
        if ($name_extension === false)
            return false;

        foreach ($image_extensions as $extension) {

            if ($extension == $name_extension) {

                $found = true;
                break;
            }
        }

        return $found;
    }

    /**
     *  Opens an archive for accessing image files.
     *
     *  @param $file_path The file path to the archive.
     *  @return An object that implements ImageArchiveInterface or FALSE on failure.
     */
    public static function open($file_path) {

        $extension = ImageArchive::getExtension($file_path);
        if ($extension == 'zip' || $extension == 'cbz')
            return new ImageArchiveZip($file_path);
        elseif ($extension == 'rar' || $extension == 'cbr')
            return new ImageArchiveRar($file_path);
        else
            return false;
    }
}
