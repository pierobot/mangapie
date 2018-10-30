<?php

namespace App;

use App\Archive;
use App\ImageArchive;
use App\Manga;

use \Carbon\Carbon;

class Image
{
    /**
     *  Makes a cover from an image buffer.
     *  Specifying a value for width or image and null for the other will
     *  result in a proportionally resized image.
     *
     *  @param string $contents The image buffer contents.
     *  @param int $width The desired width.
     *  @param int $height The desired height.
     *  @return \Intervention\Image\Image|FALSE
     *
     */
    public static function make($contents, $width, $height)
    {
        if ($width == null || $height == null)
            $image = \Image::make($contents)->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });
        else
            $image = \Image::make($contents)->resize($width, $height);

        return $image;
    }

    /**
     * Gets the images disk.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public static function disk()
    {
        return \Storage::disk('images');
    }

    /**
     * Gets the root path of the images disk.
     *
     * @return string
     */
    public static function rootPath()
    {
        return \Storage::disk('images')->path('');
    }

    /**
     * Gets the size, in bytes, of the images disk.
     *
     * @return int
     */
    public static function size()
    {
        $size = 0;
        $images = \Storage::disk('images');
        $files = $images->allFiles();

        foreach ($files as $file) {
            $size += $images->size($file);
        }

        return $size;
    }

    /**
     * @param \App\Manga $manga
     * @param \App\Archive $archive
     * @return string
     */
    public static function storagePath(Manga $manga, Archive $archive)
    {
        return \Storage::disk('images')->path(strval($manga->id) . DIRECTORY_SEPARATOR . strval($archive->id));
    }

    /**
     * @param \App\Manga $manga
     * @param \App\Archive $archive
     * @param int $page
     * @return string|false
     */
    public static function extract(Manga $manga, Archive $archive, int $page)
    {
        if (\Cache::tags(['config', 'image'])->get('extract', false) === true) {
            if (! empty($manga) && ! empty($archive) && $page > 0) {
                $path = self::storagePath($manga, $archive);

                // return true if the file has already been extracted
                if (\File::exists($path . DIRECTORY_SEPARATOR . strval($page)))
                    return true;

                if (! \File::exists($path))
                    \File::makeDirectory($path, 0775, true);

                $imgArchive = ImageArchive::open($manga->getPath() . DIRECTORY_SEPARATOR . $archive->getName());
                if ($imgArchive->good()) {
                    return $imgArchive->extract($page - 1, $path, strval($page));
                }
            }
        }

        return false;
    }

    public static function relativePath(Manga $manga, Archive $archive)
    {
        return strval($manga->id) . DIRECTORY_SEPARATOR . strval($archive->id) . DIRECTORY_SEPARATOR;
    }

    /**
     * Gets the path for the requested image.
     * The path is intended for use with X-Accel-Redirect.
     *
     * @param \App\Manga $manga
     * @param \App\Archive $archive
     * @param int $page
     * @return string
     */
    public static function xaccelPath(Manga $manga, Archive $archive, int $page)
    {
        return strval($manga->id) . DIRECTORY_SEPARATOR
            . strval($archive->id) . DIRECTORY_SEPARATOR
            . strval($page);
    }

    /**
     * Creates a response compatible with X-Accel-Redirect for an image.
     *
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public static function response(Manga $manga, Archive $archive, int $page)
    {
        if (! self::extract($manga, $archive, $page))
            return self::defaultResponse($manga, $archive, $page);

        $path = self::xaccelPath($manga, $archive, $page);
        $mime = \Storage::disk('images')->mimeType($path);

        return response()->make('', 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String(),
            'X-Accel-Redirect' => '/images/' . $path,
            'X-Accel-Charset' => 'utf-8'
        ]);
    }

    /**
     * Creates a response for an image.
     * This function is called if extraction is disabled or fails.
     *
     * @param Manga $manga
     * @param Archive $archive
     * @param int $page
     * @return \Illuminate\Http\Response
     */
    public static function defaultResponse(Manga $manga, Archive $archive, int $page)
    {
        $image = $manga->getImage($archive, $page);

        if ($image == false)
            return response()->make('Unable to read image from archive.', 400);

        return response()->make($image['contents'], 200, [
            'Content-Type' => $image['mime'],
            'Content-Length' => $image['size'],
            'Cache-Control' => 'public, max-age=2629800',
            'Expires' => Carbon::now()->addMonth()->toRfc2822String()
        ]);
    }
}
