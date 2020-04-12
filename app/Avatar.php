<?php

namespace App;

use Intervention\Image\Exception\ImageException;

final class Avatar extends StreamableStorageFile
{
    /** @var int $userId */
    private $userId;
    /**
     * Avatar constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->userId = $user->id;
        $root = storage_path('app');
        $relativePath = "avatars/{$user->id}";

        if (! file_exists($root . DIRECTORY_SEPARATOR . $relativePath)) {
            $relativePath = 'avatars/default.jpeg';
        }

        parent::__construct(
            $root,
            $relativePath
        );
    }

    /**
     * Gets the size, in bytes, of the avatars disk.
     *
     * @return int
     */
    public static function size()
    {
        $size = 0;
        $previews = \Storage::disk('avatars');
        $files = $previews->allFiles();

        foreach ($files as $file) {
            $size += $previews->size($file);
        }

        return $size;
    }

    /**
     * Puts contents as the user's avatar.
     *
     * @param string $contents
     * @return bool
     *
     * @throws ImageException
     */
    public function put(string $contents)
    {
        /** @var \Intervention\Image\Image $image */
        $image = Image::make($contents, null, 200);

        return \Storage::disk('avatars')->put(
            strval($this->userId),
            $image->stream()->detach()
        );
    }
}
