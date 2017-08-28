<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\JaroWinkler;

class JaroWinklerTest extends TestCase
{
    /**
     * Asserts that two strings are equal.
     *
     * @return void
     */
    public function testdistance_equal()
    {
        $str = 'Urusei Yatsura';
        $d = JaroWinkler::distance($str, 'Urusei Yatsura');

        $this->assertTrue($d == 1.0);
        
        $str = 'うる星やつら';
        $d = JaroWinkler::distance($str, 'うる星やつら');

        $this->assertTrue($d == 1.0);
    }

    public function testdistance()
    {
        $str = 'うる星やつら';
        
        $d1 = JaroWinkler::distance($str, 'うる星や');
        $d2 = JaroWinkler::distance($str, 'うる星やつ');

        // assert the distance is within the bounds of 0 and 1
        $this->assertTrue($d1 < 1.0);
        $this->assertTrue($d1 > 0.0);
        $this->assertTrue($d2 < 1.0);
        $this->assertTrue($d2 > 0.0);

        $this->assertTrue($d1 < $d2);

        $str = 'Urusei Yatsura';
        $d1 = JaroWinkler::distance($str, 'Urusei Ya');
        $d2 = JaroWinkler::distance($str, 'Urusei Yatsu');

        $this->assertTrue($d1 < 1.0);
        $this->assertTrue($d1 > 0.0);
        $this->assertTrue($d2 < 1.0);
        $this->assertTrue($d2 > 0.0);

        $this->assertTrue($d1 < $d2);
    }
}
