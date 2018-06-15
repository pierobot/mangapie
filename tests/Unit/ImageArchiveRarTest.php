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

        $this->assertTrue($archive_rar !== false);
        $this->assertTrue($archive_cbr !== false);

        $this->assertTrue($archive_rar->good());
        $this->assertTrue($archive_cbr->good());
    }

    public function testgetImages()
    {
        $archive_rar = ImageArchive::open($this->path_rar);
        $archive_cbr = ImageArchive::open($this->path_cbr);

        $images_rar = $archive_rar->getImages();
        $images_cbr = $archive_cbr->getImages();

        // the rar and cbr are the same archive so their layout should be the same
        $this->assertFalse(empty($images_rar));
        $this->assertFalse(empty($images_cbr));

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

        $size_rar = 0;
        $size_cbr = 0;

        // the rar and cbr should have the same file contents
        foreach ($images_rar as $index => $image_rar) {

            $image_cbr = $images_cbr[$index];

            $contents_rar = $archive_rar->getImage($image_rar['index'], $size_rar);
            $contents_cbr = $archive_cbr->getImage($image_cbr['index'], $size_cbr);

            $this->assertFalse(empty($contents_rar));
            $this->assertFalse(empty($contents_cbr));

            $this->assertEquals($contents_rar, $contents_cbr);
        }
    }
}