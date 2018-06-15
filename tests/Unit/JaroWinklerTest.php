<?php

namespace Tests\Unit;

use App\IntlString;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\JaroWinkler;

/**
 * @requires extension mbstring
 * @requires extension intl
 *
 * @covers \App\IntlString
 * @covers \App\JaroWinkler
 */
class JaroWinklerTest extends TestCase
{
    /**
     * @testWith ["Urusei Yatsura", "Urusei Yatsura"]
     */
    public function testdistanceLatinExact(string $left, $right)
    {
        $this->assertEquals(1.0, JaroWinkler::distance($left, $right));
    }

    /**
     * @testWith ["うる星やつら", "うる星やつら"]
     */
    public function testdistanceJapaneseExact(string $left, $right)
    {
        $this->assertEquals(1.0, JaroWinkler::distance($left, $right));
    }

    /**
     * @testWith ["Urusei", "Urus"]
     */
    public function testdistanceLatinNotExact(string $left, string $right)
    {
        $this->assertEquals(0.9333, floatval(number_format(JaroWinkler::distance($left, $right), 4)));
    }

    /**
     * @testWith ["うる星やつら", "うる星や"]
     */
    public function testdistanceJapaneseNotExact(string $left, string $right)
    {
        $this->assertEquals(0.9333, floatval(number_format(JaroWinkler::distance($left, $right), 4)));
    }

    /**
     * @testWith ["asd", "xyz"]
     */
    public function testdistanceLatinZero(string $left, string $right)
    {
        $this->assertEquals(0.0, JaroWinkler::distance($left, $right));
    }

    /**
     * @testWith ["あえい", "ンおう"]
     */
    public function testdistanceJapaneseZero(string $left, string $right)
    {
        $this->assertEquals(0.0, JaroWinkler::distance($left, $right));
    }
}
