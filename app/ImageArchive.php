<?php

namespace App;

use App\Interfaces\ImageArchiveInterface;

class ImageArchive
{
    public static function getExtension($name)
    {
        $extension = [];

        $result = preg_match('/\.(\w+)$/m', $name, $extension);

        return $result != 0 ? $extension[1] : false;
    }

    public static function isJunk($name)
    {
        return preg_match('/^(__MACOSX|\.DS_STORE)/m', $name) == true;
    }

    public static function isImage($name)
    {
        // there are some images in junk folders that we don't care about
        if (ImageArchive::isJunk($name) != true) {
            $image_extensions = [
                'jpg',
                'jpeg',
                'png',
                'gif' // really rare but I've come across this.
            ];

            return in_array(self::getExtension($name), $image_extensions);
        }

        return false;
    }

    /**
     *  Opens an archive for accessing image files.
     *
     *  @param string $file_path The file path to the archive.
     *  @return ImageArchiveInterface|false
     */
    public static function open($file_path)
    {
        $extension = ImageArchive::getExtension($file_path);

        if (preg_match("/zip|cbz/i", $extension))
            return new ImageArchiveZip($file_path);
        elseif (preg_match("/rar|cbr/i", $extension))
            return new ImageArchiveRar($file_path);
        else
            return false;
    }
}