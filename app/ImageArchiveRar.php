<?php

namespace App;

use \App\ImageArchive;

class ImageArchiveRar implements ImageArchiveInterface
{
    private $m_file_path;
    private $m_rar;

    public function __construct($file_path) {

        $this->m_file_path = $file_path;

        $this->m_rar = \RarArchive::open($file_path);
        if ($this->m_rar === false)
            $this->m_rar = false;
    }

    public function __destruct() {

        if ($this->m_rar !== false)
            $this->m_rar->close();
    }

    /**
     *  Used to check whether construction was ok.
     *
     *  @return TRUE if no errors occurred and FALSE otherwise.
     */
    public function good() {

        return $this->m_rar !== false;
    }

    /**
     *  Gets information about an entry at an index.
     *
     *  @return An array containing information or FALSE on failure.
     */
    public function getInfo($index) {

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
     *  @param $index The index of the file.
     *  @param &$size The variable that will hold the size of the contents.
     *  @return The contents of the file or FALSE on failure.
     */
    public function getContents($index, &$size) {

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

    /**
     *  Gets all the image entries in an archive.
     *
     *  @param $filter A callback that determines whether a file is an image.
     *  @return An array of entries or FALSE on failure.
     */
    public function getImages() {

        $images = [];

        $entries = $this->m_rar->getEntries();
        foreach ($entries as $index => $entry) {

            $name = $entry->getName();
            $size = $entry->getUnpackedSize();

            if (ImageArchive::isImage($name)) {

                array_push($images, [
                    'name' => $name,
                    'size' => $size,
                    'index' => $index
                ]);
            }
        }

        return $images;
    }
}
