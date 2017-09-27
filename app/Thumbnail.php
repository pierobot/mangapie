<?php

namespace App;

use \App\ImageArchive;

class Thumbnail
{
    /**
     *  Makes a thumbnail from an image buffer.
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
            $intervention_image = \Image::make($contents)->resize($width, $height, function($constraint) {
                $constraint->aspectRatio();
            });
        else
            $intervention_image = \Image::make($contents)->resize($width, $height);

        return $intervention_image->encode('jpg', 75);
        // if ($width == null || $height == null)
        //     $intervention_image = \Image::make($image)->resize($width, $height, function($constraint) {
        //         $constraint->aspectRatio();
        //     });
        // else
        //     $intervention_image = \Image::make($image)->resize($width, $height);
        //
        // return $intervention_image->encode('jpg', 75);
    }
}
