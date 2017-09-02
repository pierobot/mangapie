<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Theme;

class ThemeTest extends TestCase
{
    /**
     *  Asserts whether calling Theme::path returns the expected value.
     *
     * @return void
     */
    public function testpath()
    {
        $bootswatch_path = '/public/themes/bootswatch/';
        $slate_path = $bootswatch_path . 'slate/bootstrap.min.css';
        $yeti_path = $bootswatch_path . 'yeti/bootstrap.min.css';
        $lumen_path = $bootswatch_path . 'lumen/bootstrap.min.css';

        $this->assertTrue($slate_path == Theme::path('bootswatch/slate'));
        $this->assertTrue($yeti_path == Theme::path('bootswatch/yeti'));
        $this->assertTrue($lumen_path == Theme::path('bootswatch/lumen'));
    }

    public function testall()
    {
        // passing false will return the name for both index and value
        $all = Theme::all(false);

        $this->assertTrue(empty($all) === false);
        $this->assertTrue(array_key_exists("bootswatch", $all) == true);

        foreach ($all as $collection) {

            $this->assertTrue(empty($collection) === false);

            foreach ($collection as $index => $name) {

                $this->assertTrue($index == $name);
            }
        }

        $all = Theme::all();
        $this->assertTrue(empty($all) === false);
        $this->assertTrue(array_key_exists("bootswatch", $all) == true);

        foreach ($all as $collection) {

            $this->assertTrue(empty($collection) === false);

            foreach ($collection as $name => $path) {

                $this->assertTrue($name != $path);
            }
        }
    }
}
