<?php

namespace Tests\Unit;

use Tests\TestCase;

use \App\ImageArchive;

/**
 * @covers \App\ImageArchive
 */
class ImageArchiveTest extends TestCase
{
    private $path_zip = 'tests/Data/ImageArchive/zip/abc.zip';
    private $path_cbz = 'tests/Data/ImageArchive/zip/abc.cbz';

    private $path_rar = 'tests/Data/ImageArchive/rar/abc.rar';
    private $path_cbr = 'tests/Data/ImageArchive/rar/abc.cbr';

    public function testisJunk()
    {
        $this->assertFalse(ImageArchive::isJunk('asd?？asd/029.png'));
        $this->assertFalse(ImageArchive::isJunk('テスト♡テスト/050.png'));

        $this->assertTrue(ImageArchive::isJunk('__MACOSX/asd？asd/029.png'));
        $this->assertTrue(ImageArchive::isJunk('.DS_STORE/テスト♡テスト/050.png'));
    }

    public function testgetExtension()
    {
        $this->assertTrue(ImageArchive::getExtension('asd?？asd/029.png') == 'png');
        $this->assertTrue(ImageArchive::getExtension('テスト♡テスト/050.png') == 'png');
    }

    public function testisImage()
    {
        $this->assertTrue(ImageArchive::isImage('asd?？asd/029.png'));
        $this->assertTrue(ImageArchive::isImage('テスト♡テスト/050.png'));

        $this->assertFalse(ImageArchive::isImage('asd?？asd/029.asd'));
        $this->assertFalse(ImageArchive::isImage('テスト♡テスト/050.xyz'));

        $this->assertFalse(ImageArchive::isImage('__MACOSX/asd？asd/029.png'));
        $this->assertFalse(ImageArchive::isImage('.DS_STORE/テスト♡テスト/050.png'));

    }

    public function testopenFalseOnInvalidExtension()
    {
        $this->assertFalse(ImageArchive::open('asd.xyz'));
    }
}
