<?php

namespace Tests\Unit;

use Tests\TestCase;

use \App\ImageArchive;

/**
 * @covers \App\ImageArchive
 * @covers \App\ImageArchiveZip
 *
 * @requires extension zip
 */
class ImageArchiveZipTest extends TestCase
{
    private $path_zip = 'tests/Data/ImageArchive/zip/abc.zip';
    private $path_cbz = 'tests/Data/ImageArchive/zip/abc.cbz';

    public function testopen()
    {
        $archive_zip = ImageArchive::open($this->path_zip);
        $archive_cbz = ImageArchive::open($this->path_cbz);

        $this->assertTrue($archive_zip !== false);
        $this->assertTrue($archive_cbz !== false);

        $this->assertTrue($archive_zip->good());
        $this->assertTrue($archive_cbz->good());
    }

    public function testgetImages()
    {
        $archive_zip = ImageArchive::open($this->path_zip);
        $archive_cbz = ImageArchive::open($this->path_cbz);

        $images_zip = $archive_zip->getImages();
        $images_cbz = $archive_cbz->getImages();

        // the zip and cbz are the same archive so their layout should be the same
        $this->assertFalse(empty($images_zip));
        $this->assertFalse(empty($images_cbz));

        $this->assertEquals(count($images_zip), count($images_cbz));

        foreach ($images_zip as $index => $image) {

            $this->assertEquals($images_zip[$index]['name'], $images_cbz[$index]['name']);
            $this->assertEquals($images_zip[$index]['size'], $images_cbz[$index]['size']);
            $this->assertEquals($images_zip[$index]['index'], $images_cbz[$index]['index']);
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

        // the zip and cbz should have the same file contents
        foreach ($images_zip as $index => $image_zip) {

            $image_cbz = $images_cbz[$index];

            $contents_zip = $archive_zip->getImage($image_zip['index'], $size_zip);
            $contents_cbz = $archive_cbz->getImage($image_cbz['index'], $size_cbz);

            $this->assertFalse(empty($contents_zip));
            $this->assertFalse(empty($contents_cbz));

            $this->assertEquals($contents_zip, $contents_cbz);
        }
    }
}