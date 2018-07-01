<?php

namespace App;

use \Carbon\Carbon;

use App\Image;
use App\User;

class Avatar
{
    /**
     * Gets the root path of the avatars disk.
     *
     * @return string
     */
    public static function rootPath()
    {
        return \Storage::disk('avatars')->path('');
    }

    /**
     * Gets the path for the requested avatar.
     * The path is intended for use with X-Accel-Redirect.
     *
     * @param User $user
     * @return string
     */
    public static function xaccelPath(User $user)
    {
        return strval($user->getId());
    }

    /**
     * Gets the path of the default avatar.
     * The path is intended for use with X-Accel-Redirect.
     *
     * @return string
     */
    public static function xaccelDefaultPath()
    {
        return 'default.jpg';
    }

    /**
     * Determine if an avatar exists for a user.
     *
     * @param User $user
     * @return bool
     */
    public static function exists(User $user)
    {
        return \Storage::disk('avatars')->exists(self::xaccelPath($user));
    }

    /**
     * Saves the contents of an image to the appropriate path.
     *
     * @param string $contents The raw contents of the avatar.
     * @param User $user
     * @return bool
     */
    public static function save(string $contents, User $user)
    {
        try {
            $image = Image::make($contents, null, 200);
        } catch (\Intervention\Image\Exception\ImageException $e) {
            return false;
        }

        $path = self::xaccelPath($user);

        return \Storage::disk('avatars')->putStream(
            $path,
            $image->stream()->detach());
    }

    /**
     * Creates a response compatible with X-Accel-Redirect for an avatar.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public static function response(User $user)
    {
        $path = self::xaccelPath($user);
        $mime = \Storage::disk('avatars')->mimeType($path);

        return response()->make('', 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=259200',
            'Expires' => Carbon::now()->addDays(3)->toRfc2822String(),
            'X-Accel-Redirect' => '/avatars/' . $path,
            'X-Accel-Charset' => 'utf-8'
        ]);
    }

    /**
     * Creates a response compatible with X-Accel-Redirect for the default avatar.
     *
     * @return \Illuminate\Http\Response
     */
    public static function defaultResponse()
    {
        $path = self::xaccelDefaultPath();
        $mime = \Storage::disk('avatars')->mimeType($path);

        return response()->make('', 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=259200',
            'Expires' => Carbon::now()->addDays(3)->toRfc2822String(),
            'X-Accel-Redirect' => '/avatars/' . $path,
            'X-Accel-Charset' => 'utf-8'
        ]);
    }
}
