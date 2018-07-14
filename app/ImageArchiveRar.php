<?php

namespace App;

use App\Interfaces\ImageArchiveInterface;

class ImageArchiveRar implements ImageArchiveInterface
{
    /**
     * @var string
     */
    private $m_file_path;

    /**
     * @var \RarArchive
     */
    private $m_rar;

    public function __construct($file_path)
    {
        $this->m_file_path = $file_path;

        try {
            $this->m_rar = \RarArchive::open($file_path);
        } catch (\Exception $e) {
            $this->m_rar = false;
        }
    }

    public function __destruct()
    {
        if ($this->m_rar !== false)
            $this->m_rar->close();
    }

    /**
     *  Used to check whether construction was ok.
     *
     *  @return bool TRUE if no errors occurred and FALSE otherwise.
     */
    public function good()
    {
        return $this->m_rar !== false;
    }

    /**
     *  Gets information about an entry at an index.
     *
     *  @index int $index The index of the desired entry.
     *  @return mixed An array containing information or FALSE on failure.
     */
    public function getInfo($index)
    {

        $entries = $this->m_rar->getEntries();
        foreach ($entries as $idx => $entry) {
            if ($index == $idx) {
                $name = $entry->getName();
                $size = $entry->getUnpackedSize();

                return [
                    'name' => $name,
                    'size' => $size
                ];
            }
        }

        return false;
    }

    /**
     *  Gets the contents of a file at an index.
     *
     *  @param int $index The index of the file.
     *  @param int &$size The variable that will hold the size of the contents.
     *  @return mixed The contents of the file or FALSE on failure.
     */
    public function getImage($index, &$size)
    {
        $entries = $this->m_rar->getEntries();
        foreach ($entries as $idx => $entry) {

            if ($index == $idx) {
                $stream = $entry->getStream();
                $size = $entry->getUnpackedSize();

                return stream_get_contents($stream, $size);
            }
        }

        return false;
    }

    public function getImageUrlPath($index, &$size)
    {
        $entries = $this->m_rar->getEntries();
        foreach ($entries as $idx => $entry) {
            if ($index == $idx) {
                $size = $entry->getUnpackedSize();

                return 'rar://' . rawurlencode($this->m_file_path) . '#' . rawurlencode($entry->getName());
            }
        }

        return false;
    }

    /**
     *  Gets all the image entries in an archive.
     *
     *  @return mixed An array of entries or FALSE on failure.
     */
    public function getImages()
    {
        $images = [];

        $entries = $this->m_rar->getEntries();
        foreach ($entries as $index => $entry) {
            $name = $entry->getName();
            $size = $entry->getUnpackedSize();

            if (ImageArchive::isImage($name)) {
                $images[] = [
                    'name' => $name,
                    'size' => $size,
                    'index' => $index
                ];
            }
        }

        return $images;
    }

    /**
     * Extracts the image at the given index to the given path.
     *
     * @param $index
     * @param $path
     * @param $name
     * @return bool
     */
    public function extract($index, $path, $name)
    {
        $images = $this->getImages();
        if ($index >= 0 && (count($images) - 1 >= $index) && ! empty($path)) {
            usort($images, function ($left, $right) {
                return strnatcasecmp($left['name'], $right['name']);
            });

            $entryName = $images[$index]['name'];
            $destPath = $path . DIRECTORY_SEPARATOR . $name;
            $size = 0;

            return $this->m_rar->getEntry($entryName)->extract(false, $destPath);
//            return copy($this->getImageUrlPath($index, $size), $destPath);
        }

        return false;
    }
}
