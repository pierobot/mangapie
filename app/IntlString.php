<?php

namespace App;

# requirements: php-intl

class IntlString {

    /**
     *  Converts a string to the specified encoding.
     *  PHP documentation is terrible, but I believe the supported
     *  encodings are some of the predefined constants at http://php.net/manual/en/class.uconverter.php
     *
     *  @param $str1 The string to convert.
     *  @param $encoding The encoding to use.
     */
    public static function convert($str1, $encoding = 'UTF-8') {

        $converter = new \UConverter($encoding);

        return $converter->convert($str1);
    }

    /**
     *  Gets the grapheme length of a string.
     *  The length is not the number of bytes but the count of grapheme units.
     *
     *  $str1 A string.
     *  @return The length of $str1.
     */
    public static function strlen($str1) {

        return grapheme_strlen($str1);
    }

    /**
     *  Extracts the grapheme of a string at an offset.
     *  An offset is not a character index but a byte offset.
     *  For example, for a UTF-8 encoded string where all the graphemes are 16 bits, an offset of:
     *      0 will return the first grapheme,
     *      2 will return the second grapheme,
     *      4 will return the third grapheme,
     *      6 will return the fourth grapheme.
     *
     *  @param $str1 The string from which to extract a grapheme.
     *  @param $offset The starting offset of a grapheme.
     *  @param $next A reference to a variable that will hold the offset of the next grapheme.
     */
    public static function grapheme(string $str1, int $offset, int &$next) {

        return grapheme_extract($str1, 1, GRAPHEME_EXTR_MAXCHARS, $offset, $next);
    }

    /**
     *  Compares two strings.
     *
     *  @param $str1 A string.
     *  @param $str2 A string.
     *  @return An integer that indicates lexicographical difference between the two strings.
     *          A value of zero indicates they are lexicographically equal.
     *          A negative value indicates $str1 is lexicographically less than $str2.
     *          A positive value indicates $str2 is lexicographically greater than $str2.
     */
    public static function strcmp($str1, $str2) {

        // iterate until $current_1 and $current_2 reach the $min grapheme length
        for ($current_1 = 0, $current_2 = 0, $next_1 = 0, $next_2 = 0;;) {

            // get the grapheme for the current pos and advance $current_x to $next_x
            // this will let us get the next grapheme on the next call
            $g_1 = IntlString::grapheme($str1, ($current_1 = $next_1), $next_1);
            $g_2 = IntlString::grapheme($str2, ($current_2 = $next_2), $next_2);
            if ($g_1 === false && $g_2 === false)
                break;

            // get the code point for the grapheme for comparison
            $cp_1 = \IntlChar::ord($g_1);
            $cp_2 = \IntlChar::ord($g_2);

            if ($cp_1 != $cp_2)
                return $cp_1 - $cp_2;
        }

        // strings are equal if execution reaches here
        return 0;
    }

    /**
     *  Compares two strings up to count grapheme units.
     *
     *  @param $str1 A string.
     *  @param $str2 A string.
     *  @param $count The count of grapheme units to compare.
     *  @return An integer that indicates lexicographical difference between the two strings.
     *          A value of zero indicates they are lexicographically equal.
     *          A negative value indicates $str1 is lexicographically less than $str2.
     *          A positive value indicates $str2 is lexicographically greater than $str2.
     */
    public static function strncmp($str1, $str2, $count) {

        // iterate until $current_1 and $current_2 reach the $min grapheme length
        for ($i = 0, $current_1 = 0, $current_2 = 0, $next_1 = 0, $next_2 = 0; $i < $count; $i++) {

            // get the grapheme for the current pos and advance $current_x to $next_x
            // this will let us get the next grapheme on the next call
            $g_1 = IntlString::grapheme($str1, ($current_1 = $next_1), $next_1);
            $g_2 = IntlString::grapheme($str2, ($current_2 = $next_2), $next_2);
            if ($g_1 === false && $g_2 === false)
                break;

            // get the code point for the grapheme for comparison
            $cp_1 = \IntlChar::ord($g_1);
            $cp_2 = \IntlChar::ord($g_2);

            if ($cp_1 != $cp_2)
                return $cp_1 - $cp_2;
        }

        // strings are equal if execution reaches here
        return 0;
    }
}
