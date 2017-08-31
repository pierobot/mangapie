<?php

namespace App;

use \ZipArchive;
use \App\ImageArchive;

class ImageArchiveZip implements ImageArchiveInterface
{
    private $m_file_path;
    private $m_zip;

    public function __construct($file_path) {

        $this->m_file_path = $file_path;

        $this->m_zip = new ZipArchive;
        if ($this->m_zip->open($this->m_file_path) === false)
            $this->m_zip = false;
    }

    public function __destruct() {

        if ($this->m_zip !== false)
            $this->m_zip->close();
    }

    /**
     *  Used to check whether construction was ok.
     *
     *  @return TRUE if no errors occurred and FALSE otherwise.
     */
    public function good() {

        return $this->m_zip !== false;
    }

    /**
     *  Gets information about an entry at an index.
     *
     *  @return An array containing zip_stat_t information or FALSE on failure.
     */
    public function getInfo($index) {

        $stat = $this->m_zip->statIndex($index);

        return $stat;
    }

    /**
     *  Gets the contents of a file at an index.
     *
     *  @param $index The index of the file.
     *  @param &$size The variable that will hold the size of the contents.
     *  @return The contents of the file or FALSE on failure.
     */
    public function getContents($index, &$size) {

        $images = $this->getImages();
        if ($images === false)
            return false;

        $data = $this->getInfo($index);
        if ($data === false)
            return false;

        $contents = $this->m_zip->getFromIndex($index);
        if ($contents === false)
            return false;

        $size = $data['size'];

        return $contents;
    }

    /**
     *  Gets all the image entries in an archive.
     *
     *  @param $filter A callback that determines whether a file is an image.
     *  @return An array of entries or FALSE on failure.
     */
    public function getImages() {

        $images = [];

        for ($i = 0; $i < $this->m_zip->numFiles; $i++) {

            $data = $this->getInfo($i);

            if ($data === false)
                continue;

            $name = $data['name'];
            $size = $data['size'];

            if (ImageArchive::isImage($name)) {

                array_push($images, [
                    'name' => $name,
                    'size' => $size,
                    'index' => $i
                ]);
            }
        }

        return $images;
    }
}
