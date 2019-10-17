<?php

namespace App;

use \Carbon\Carbon;

/* TODO: Simplify preview and Preview classes. */

class Preview
{
    /**
     * Gets the previews disk.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public static function disk()
    {
        return \Storage::disk('previews');
    }

    /**
     * Gets the root path of the previews disk.
     *
     * @return string
     */
    public static function rootPath()
    {
        return \Storage::disk('previews')->path('');
    }

    /**
     * Gets the size, in bytes, of the previews disk.
     *
     * @return int
     */
    public static function size()
    {
        $size = 0;
        $previews = \Storage::disk('previews');
        $files = $previews->allFiles();

        foreach ($files as $file) {
            $size += $previews->size($file);
        }

        return $size;
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
    public static function xaccelPath(Manga $manga, Archive $archive, int $page, bool $small = true)
    {
        return ($small === true ? 'small' : 'medium') . DIRECTORY_SEPARATOR
            . strval($manga->getId()) . DIRECTORY_SEPARATOR
            . strval($archive->getId()) . DIRECTORY_SEPARATOR
            . strval($page);
    }

    /**
     * Determine if a preview exists for an archive.
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

        return \Storage::disk('previews')->exists(self::xaccelPath($manga, $archive, $page, $small));
    }

    /**
     * Saves the contents of an image to the appropriate path.
     *
     * @param string $contents The raw contents of the preview.
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @param bool $small
     * @return bool
     */
    public static function save(string $contents, Manga $manga, Archive $archive, int $page, bool $small = true)
    {
        $path = self::xaccelPath($manga, $archive, $page, $small);

        try {
            $image = Image::make($contents, null, ($small === true ? 250 : 500));
        } catch (\Intervention\Image\Exception\ImageException $e) {
            return false;
        }

        return \Storage::disk('previews')->putStream(
            $path,
            $image->stream('jpg')->detach());
    }

    /**
     * Creates a response compatible with X-Accel-Redirect for a preview.
     *
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @param bool $small
     * @return \Illuminate\Http\Response
     */
    public static function response(Manga $manga, Archive $archive, int $page, bool $small = true)
    {
        $path = self::xaccelPath($manga, $archive, $page, $small);
        $mime = \Storage::disk('previews')->mimeType($path);

        return response()->make('', 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String(),
            'X-Accel-Redirect' => '/previews/' . $path,
            'X-Accel-Charset' => 'utf-8'
        ]);
    }
}
