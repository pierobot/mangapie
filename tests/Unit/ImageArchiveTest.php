<?php

namespace Tests\Unit;

use Tests\TestCase;

use \App\ImageArchive;

class ImageArchiveTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    private $path_zip = 'tests/Data/ImageArchive/zip/abc.zip';
    private $path_cbz = 'tests/Data/ImageArchive/zip/abc.cbz';

    private $path_rar = 'tests/Data/ImageArchive/rar/abc.rar';
    private $path_cbr = 'tests/Data/ImageArchive/rar/abc.cbr';

    public function testisJunk()
    {
        $this->assertTrue(ImageArchive::isJunk('asd?？asd/029.png') === false);
        $this->assertTrue(ImageArchive::isJunk('テスト♡テスト/050.png') === false);

        $this->assertTrue(ImageArchive::isJunk('__MACOSX/asd？asd/029.png') === true);
        $this->assertTrue(ImageArchive::isJunk('.DS_STORE/テスト♡テスト/050.png') === true);
    }

    public function testgetExtension()
    {
        $this->assertTrue(ImageArchive::getExtension('asd?？asd/029.png') == 'png');
        $this->assertTrue(ImageArchive::getExtension('テスト♡テスト/050.png') == 'png');
    }

    public function testopen()
    {
        $archive_zip = ImageArchive::open($this->path_zip);
        $archive_cbz = ImageArchive::open($this->path_cbz);

        $this->assertTrue($archive_zip !== false);
        $this->assertTrue($archive_cbz !== false);

        $this->assertTrue($archive_zip->good());
        $this->assertTrue($archive_cbz->good());

        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $this->assertTrue($archive_rar !== false);
        $this->assertTrue($archive_cbr !== false);

        $this->assertTrue($archive_rar->good());
        $this->assertTrue($archive_cbr->good());
    }

    public function testgetImages()
    {
        $archive_zip = ImageArchive::open($this->path_zip);
        $archive_cbz = ImageArchive::open($this->path_cbz);

        $images_zip = $archive_zip->getImages();
        $images_cbz = $archive_cbz->getImages();

        // the zip and cbz are the same archive so their layout should be the same
        $this->assertTrue(empty($images_zip) === false);
        $this->assertTrue(empty($images_cbz) === false);

        $this->assertTrue(count($images_zip) == count($images_cbz));

        foreach ($images_zip as $index => $image) {

            $this->assertTrue($images_zip[$index]['name'] == $images_cbz[$index]['name']);
            $this->assertTrue($images_zip[$index]['size'] == $images_cbz[$index]['size']);
            $this->assertTrue($images_zip[$index]['index'] == $images_cbz[$index]['index']);
        }

        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $images_rar = $archive_rar->getImages();
        $images_cbr = $archive_cbr->getImages();

        // the rar and cbr are the same archive so their layout should be the same
        $this->assertTrue(empty($images_rar) === false);
        $this->assertTrue(empty($images_cbr) === false);

        $this->assertTrue(count($images_rar) == count($images_cbr));

        foreach ($images_rar as $index => $image) {

            $this->assertTrue($images_rar[$index]['name'] == $images_cbr[$index]['name']);
            $this->assertTrue($images_rar[$index]['size'] == $images_cbr[$index]['size']);
            $this->assertTrue($images_rar[$index]['index'] == $images_cbr[$index]['index']);
        }
    }

    public function testgetImage()
    {
        $archive_zip = ImageArchive::open($this->path_zip);
        $archive_cbz = ImageArchive::open($this->path_cbz);

        $images_zip = $archive_zip->getImages();
        $images_cbz = $archive_cbz->getImages();

        $size_zip = 0;
        $size_cbz = 0;
        $contents_zip = '';
        $contents_cbz = '';

        // the zip and cbz should have the same file contents
        foreach ($images_zip as $index => $image_zip) {

            $image_cbz = $images_cbz[$index];

            $contents_zip = $archive_zip->getImage($image_zip['index'], $size_zip);
            $contents_cbz = $archive_cbz->getImage($image_cbz['index'], $size_cbz);

            $this->assertTrue(empty($contents_zip) !== true);
            $this->assertTrue(empty($contents_cbz) !== true);

            $this->assertTrue($contents_zip == $contents_cbz);
        }

        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $images_rar = $archive_rar->getImages();
        $images_cbr = $archive_cbr->getImages();

        $size_rar = 0;
        $size_cbr = 0;

        // the rar and cbr should have the same file contents
        foreach ($images_rar as $index => $image_rar) {

            $image_cbr = $images_cbr[$index];

            $contents_rar = $archive_rar->getImage($image_rar['index'], $size_rar);
            $contents_cbr = $archive_cbr->getImage($image_cbr['index'], $size_cbr);

            $this->assertTrue(empty($contents_rar) !== true);
            $this->assertTrue(empty($contents_cbr) !== true);

            $this->assertTrue($contents_rar == $contents_cbr);
        }
    }
}
