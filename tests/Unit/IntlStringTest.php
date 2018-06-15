<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\IntlString;

/**
 * @requires extension intl
 *
 * @covers \App\IntlString
 */
class IntlStringTest extends TestCase
{
    /**
     * @testWith ["Maison Ikkoku"]
     */
    public function testconvertLatinToUTF8(string $str)
    {
        $convertedStr = IntlString::convert($str);
        $this->assertEquals('ASCII', mb_detect_encoding($str));
    }

    /**
     * @testWith ["めぞん一刻"]
     */
    public function testconvertJapaneseToUTF8(string $str)
    {
        $convertedStr = IntlString::convert($str);
        $this->assertEquals('UTF-8', mb_detect_encoding($str));
    }

    /**
     * @testWith ["Maison Ikkoku"]
     */
    public function teststrlenLatin(string $str)
    {
        $this->assertTrue(IntlString::strlen($str) === 13);
    }

    /**
     * @testWith ["めぞん一刻"]
     */
    public function teststrlenJapanese(string $str)
    {
        $this->assertTrue(IntlString::strlen($str) === 5);
    }

    public function teststrcmpEmptyStringEqual()
    {
        $this->assertTrue(IntlString::strcmp('', '') === 0);
    }

    /**
     * @testWith ["Maison Ikkoku", "Maison Ikkoku"]
     */
    public function teststrcmpLatinEqual(string $left, string $right)
    {
        $this->assertTrue(IntlString::strcmp($left, $right) === 0);
    }

    /**
     * @testWith ["Maison", "Maison Ikkoku"]
     */
    public function teststrcmpLatinLess(string $left, string $right)
    {
        $this->assertTrue(IntlString::strcmp($left, $right) < 0);
    }

    /**
     * @testWith ["Maison Ikkoku", "Maison"]
     */
    public function teststrcmpLatinGreater(string $left, string $right)
    {
        $this->assertTrue(IntlString::strcmp($left, $right) > 0);
    }

    /**
     * @testWith ["めぞん一刻", "めぞん一刻"]
     */
    public function teststrcmpJapaneseEqual(string $left, string $right)
    {
        $this->assertTrue(IntlString::strcmp($left, $right) === 0);
    }

    /**
     * @testWith ["めぞん一", "めぞん一刻"]
     */
    public function teststrcmpJapaneseLess(string $left, string $right)
    {
        $this->assertTrue(IntlString::strcmp($left, $right) < 0);
    }

    /**
     * @testWith ["めぞん一刻", "めぞん一"]
     */
    public function teststrcmpJapaneseGreater(string $left, string $right)
    {
        $this->assertTrue(IntlString::strcmp($left, $right) > 0);
    }

    public function teststrncmpEmptyStringEqual()
    {
        $this->assertTrue(IntlString::strncmp('', '', 10) === 0);
    }

    /**
     * @testWith ["Maison Ikkoku", "Maison Ikkoku"]
     */
    public function teststrncmpLatinEqual(string $left, string $right)
    {
        $this->assertTrue(IntlString::strncmp($left, $right, 13) === 0);
    }

    /**
     * @testWith ["Maison", "Maison Ikkoku"]
     */
    public function teststrncmpLatinLess(string $left, string $right)
    {
        $this->assertTrue(IntlString::strncmp($left, $right, 13) < 0);
    }

    /**
     * @testWith ["Maison Ikkoku", "Maison"]
     */
    public function teststrncmpLatinGreater(string $left, string $right)
    {
        $this->assertTrue(IntlString::strncmp($left, $right, 13) > 0);
    }

    /**
     * @testWith ["めぞん一刻", "めぞん一刻"]
     */
    public function teststrncmpJapaneseEqual(string $left, string $right)
    {
        $this->assertTrue(IntlString::strncmp($left, $right, 5) === 0);
    }

    /**
     * @testWith ["めぞん一", "めぞん一刻"]
     */
    public function teststrncmpJapaneseLess(string $left, string $right)
    {
        $this->assertTrue(IntlString::strncmp($left, $right, 5) < 0);
    }

    /**
     * @testWith ["めぞん一刻", "めぞん一"]
     */
    public function teststrncmpJapaneseGreater(string $left, string $right)
    {
        $this->assertTrue(IntlString::strncmp($left, $right, 5) > 0);
    }

    /**
     * @testWith ["めぞん一刻"]
     */
     public function testgrapheme($str)
     {
        $current = 0;
        $next = 0;

        $this->assertTrue(IntlString::grapheme($str, ($current = $next), $next) == 'め');
        $this->assertTrue(IntlString::grapheme($str, ($current = $next), $next) == 'ぞ');
        $this->assertTrue(IntlString::grapheme($str, ($current = $next), $next) == 'ん');
        $this->assertTrue(IntlString::grapheme($str, ($current = $next), $next) == '一');
        $this->assertTrue(IntlString::grapheme($str, ($current = $next), $next) == '刻');
     }
}
