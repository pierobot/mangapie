<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\IntlString;

class IntlStringTest extends TestCase
{
    /**
     * Asserts that two strings are equal.
     *
     * @return void
     */
    public function teststrcmp_equal()
    {
        $this->assertTrue(IntlString::strcmp('Maison Ikkoku', 'Maison Ikkoku') == 0);
        $this->assertTrue(IntlString::strcmp('めぞん一刻', 'めぞん一刻') == 0);
    }

    /**
     * Asserts that one string is less than the other.
     *
     * @return void
     */
    public function teststrcmp_less()
    {
        $this->assertTrue(IntlString::strcmp('Maison', 'Maison Ikkoku') < 0);
        $this->assertTrue(IntlString::strcmp('めぞん一', 'めぞん一刻') < 0);
    }

    /**
     * Asserts that one string is greater than the other.
     *
     * @return void
     */
    public function teststrcmp_greater()
    {
        $this->assertTrue(IntlString::strcmp('Maison Ikkoku', 'Maison') > 0);
        $this->assertTrue(IntlString::strcmp('めぞん一刻', 'めぞん一') > 0);
    }

    /**
     * Asserts that that the graphemes of a string are correct.
     *
     * @return void
     */
     public function testgrapheme()
     {
        $current = 0;
        $next = 0;

        $this->assertTrue(IntlString::grapheme('めぞん一刻', ($current = $next), $next) == 'め');
        $this->assertTrue(IntlString::grapheme('めぞん一刻', ($current = $next), $next) == 'ぞ');
        $this->assertTrue(IntlString::grapheme('めぞん一刻', ($current = $next), $next) == 'ん');
        $this->assertTrue(IntlString::grapheme('めぞん一刻', ($current = $next), $next) == '一');
        $this->assertTrue(IntlString::grapheme('めぞん一刻', ($current = $next), $next) == '刻');
     }
}
