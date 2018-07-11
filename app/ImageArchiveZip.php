<?php

namespace App;

use \ZipArchive;

use App\Interfaces\ImageArchiveInterface;

class ImageArchiveZip implements ImageArchiveInterface
{
    /**
     * @var string
     */
    private $m_file_path;

    /**
     * @var ZipArchive
     */
    private $m_zip;

    public function __construct($file_path)
    {
        $this->m_file_path = $file_path;

        $this->m_zip = new ZipArchive;
        if ($this->m_zip->open($this->m_file_path) !== true)
            $this->m_zip = false;
    }

    public function __destruct()
    {
        if ($this->m_zip !== false)
            $this->m_zip->close();
    }

    /**
     *  Used to check whether construction was ok.
     *
     *  @return bool TRUE if no errors occurred and FALSE otherwise.
     */
    public function good()
    {
        return $this->m_zip !== false;
    }

    /**
     *  Gets information about an entry at an index.
     *
     *  @param int $index The index of the entry.
     *  @return mixed An array containing zip_stat_t information or FALSE on failure.
     */
    public function getInfo($index)
    {
        $stat = $this->m_zip->statIndex($index);

        return $stat;
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
        $images = $this->getImages();
        $data = $this->getInfo($index);
        $contents = false;

        if (! empty($images) && ! empty($data)) {
            $size = $data['size'];
            $contents = $this->m_zip->getFromIndex($index);
        }

        return $contents;
    }

    public function getImageUrlPath($index, &$size)
    {
        $data = $this->getInfo($index);

        if (! empty($data)) {
            $size = $data['size'];
            $name = $data['name'];

            return 'zip://' . rawurlencode($this->m_file_path) . '#' . rawurlencode($name);
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

        for ($i = 0; $i < $this->m_zip->numFiles; $i++) {
            $data = $this->getInfo($i);

            if (! empty($data)) {
                $name = $data['name'];
                $size = $data['size'];

                if (ImageArchive::isImage($name)) {
                    $images[] = [
                        'name' => $name,
                        'size' => $size,
                        'index' => $i
                    ];
                }
            }
        }

        return $images;
    }
}
