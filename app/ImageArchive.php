<?php

namespace App;

use App\Interfaces\ImageArchiveInterface;

class ImageArchive
{
    /**
     * Gets the extension from a file name.
     *
     * @param string $name
     * @return string|false
     */
    public static function getExtension(string $name)
    {
        $extension = [];

        $result = preg_match('/\.(\w+)$/', $name, $extension);

        return $result && ! empty($extension) ? $extension[1] : false;
    }

    /**
     * Indicates whether an image should be considered useless based on its name.
     *
     * @param string $name
     * @return bool
     */
    public static function isJunk(string $name) : bool
    {
        return preg_match('/^(__MACOSX|\.DS_STORE)/', $name) != false;
    }

    /**
     * Indicates whether a file is an image or not based on its name.
     *
     * @param $name
     * @return bool
     */
    public static function isImage(string $name) : bool
    {
        // there are some images in junk folders that we don't care about
        return ! ImageArchive::isJunk($name) ?
            preg_match('/jpe?g|png|gif/i', self::getExtension($name)) :
            false;
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

        if (preg_match('/zip|cbz/i', $extension))
            return new ImageArchiveZip($file_path);
        elseif (preg_match('/rar|cbr/i', $extension))
            return new ImageArchiveRar($file_path);
        else
            return false;
    }
}