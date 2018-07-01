<?php

namespace App;

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
     *  @return \Intervention\Image\Image or FALSE
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
}
