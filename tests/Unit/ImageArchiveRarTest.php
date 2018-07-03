<?php

namespace Tests\Unit;

use Tests\TestCase;

use \App\ImageArchive;

/**
 * @covers \App\ImageArchive
 * @covers \App\ImageArchiveRar
 *
 * @requires extension rar
 */
class ImageArchiveRarTest extends TestCase
{
    private $path_rar = 'tests/Data/ImageArchive/rar/abc.rar';
    private $path_cbr = 'tests/Data/ImageArchive/rar/abc.cbr';

    public function testopen()
    {
        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $this->assertNotFalse($archive_rar);
        $this->assertNotFalse($archive_cbr);

        $this->assertTrue($archive_rar->good());
        $this->assertTrue($archive_cbr->good());
    }

    public function testgoodFailsOnNonexistingFile()
    {
        $archive_rar = ImageArchive::open('this.does.not.exist.rar');
        $archive_cbr = ImageArchive::open('this.does.not.exist.cbr');

        $this->assertNotFalse($archive_rar);
        $this->assertNotFalse($archive_cbr);

        $this->assertFalse($archive_rar->good());
        $this->assertFalse($archive_cbr->good());
    }

    public function testgetImages()
    {
        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $images_rar = $archive_rar->getImages();
        $images_cbr = $archive_cbr->getImages();

        // the rar and cbr are the same archive so their layout should be the same
        $this->assertNotEmpty($images_rar);
        $this->assertNotEmpty($images_cbr);

        $this->assertEquals(count($images_rar), count($images_cbr));

        foreach ($images_rar as $index => $image) {

            $this->assertEquals($images_rar[$index]['name'], $images_cbr[$index]['name']);
            $this->assertEquals($images_rar[$index]['size'], $images_cbr[$index]['size']);
            $this->assertEquals($images_rar[$index]['index'], $images_cbr[$index]['index']);
        }
    }

    public function testgetImage()
    {
        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $images_rar = $archive_rar->getImages();
        $images_cbr = $archive_cbr->getImages();

        $this->assertNotEmpty($images_rar);
        $this->assertNotEmpty($images_cbr);

        $this->assertEquals(count($images_rar), count($images_cbr));

        $size_rar = 0;
        $size_cbr = 0;

        // the rar and cbr should have the same file contents
        foreach ($images_rar as $index => $image_rar) {

            $image_cbr = $images_cbr[$index];

            $contents_rar = $archive_rar->getImage($image_rar['index'], $size_rar);
            $contents_cbr = $archive_cbr->getImage($image_cbr['index'], $size_cbr);

            $this->assertNotEmpty($contents_rar);
            $this->assertNotEmpty($contents_cbr);

            $this->assertEquals($contents_rar, $contents_cbr);
        }
    }

    public function testgetImageUrlPath()
    {
        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $images_rar = $archive_rar->getImages();
        $images_cbr = $archive_cbr->getImages();

        $this->assertNotEmpty($images_rar);
        $this->assertNotEmpty($images_cbr);

        $this->assertEquals(count($images_rar), count($images_cbr));

        $size = 0;

        foreach ($images_rar as $image_rar) {
            $expectedUrl = 'rar://' . rawurlencode($this->path_rar) . '#' . rawurlencode($image_rar['name']);
            $actualUrl = $archive_rar->getImageUrlPath($image_rar['index'], $size);

            $this->assertEquals($expectedUrl, $actualUrl);
        }

        foreach ($images_cbr as $index => $image_cbr) {
            $expectedUrl = 'rar://' . rawurlencode($this->path_cbr) . '#' . rawurlencode($image_cbr['name']);
            $actualUrl = $archive_cbr->getImageUrlPath($image_cbr['index'], $size);

            $this->assertEquals($expectedUrl, $actualUrl);
        }
    }
}