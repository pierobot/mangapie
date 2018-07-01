<?php

namespace App;

class Cover
{
    /**
     *  Makes a cover from an image buffer.
     *  Specifying a value for width or image and null for the other will
     *  result in a proportionally resized image.
     *
     *  @param string $contents The image buffer contents.
     *  @param int $width The desired width.
     *  @param int $height The desired height.
     *  @return mixed An instance of Intervention/Image that has been resized or FALSE on failure.
     */
    public static function make($contents, $width, $height)
    {
        if ($width == null || $height == null)
            $image = \Image::make($contents)->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });
        else
            $image = \Image::make($contents)->resize($width, $height);

        return $image->encode('jpg', 75);
    }

    public static function storage_path()
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'covers');
    }

    /**
     * @param Manga $manga
     * @param Archive $archive
     * @param bool $small
     * @return bool
     */
    public static function createPath(Manga $manga, Archive $archive, bool $small = true)
    {
        $path = self::storage_path() . DIRECTORY_SEPARATOR
            . ($small === true ? 'small' : 'medium') . DIRECTORY_SEPARATOR
            . strval($manga->getId()) . DIRECTORY_SEPARATOR
            . strval($archive->getId());

        return \File::exists($path) ? false : \File::makeDirectory($path, 755, true);
    }

    /**
     * Gets the path for the requested archive image.
     * The path is intended for use with X-Accel-Redirect.
     * Essentially, this will return (small|medium)/manga_id/archive_id/page
     *
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @param bool $small
     * @return string
     */
    public static function xaccelPath(Manga $manga, Archive $archive, int $page = 1, bool $small = true)
    {
        return ($small === true ? 'small' : 'medium') . DIRECTORY_SEPARATOR
            . strval($manga->getId()) . DIRECTORY_SEPARATOR
            . strval($archive->getId()) . DIRECTORY_SEPARATOR
            . strval($page);
    }

    /**
     * Gets the path of the default archive image.
     * The path is intended for use with X-Accel-Redirect.
     *
     * @param bool $small
     * @return string
     */
    public static function xaccelDefaultPath(bool $small = true)
    {
        return ($small === true ? 'small' : 'medium') . DIRECTORY_SEPARATOR . 'unknown.jpg';
    }

    /**
     * Determine if a cover exists for an archive.
     *
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @param bool $small
     * @return bool
     */
    public static function exists(Manga $manga, Archive $archive = null, int $page = 1, bool $small = true)
    {
        if (empty($archive))
            $archive = $manga->archives->first();

        $coverPath = self::storage_path(). DIRECTORY_SEPARATOR . self::xaccelPath($manga, $archive, $page, $small);

        return \File::exists($coverPath);
    }
}
