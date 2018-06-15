<?php

namespace App;

use \App\IntlString;

class JaroWinkler {

    // gets the maximum range two matching codepoints are allowed to be apart
    private static function max_range($str1_len, $str2_len)
    {
        return (int)(floor(max($str1_len, $str2_len) / 2)) - 1;
    }

    // gets the matching codepoints in an array
    private static function matching_cp($str1, $str1_len, $str2, $str2_len, $range)
    {
        $matches = [];

        $nextGraphemeOffset1 = 0;
        for ($i = 0; $i < $str1_len; $i++) {

            $currentGraphemeOffset1 = $nextGraphemeOffset1;
            $grapheme1 = IntlString::grapheme($str1, $currentGraphemeOffset1, $nextGraphemeOffset1);

            $currentGraphemeOffset2 = 0;
            $nextGraphemeOffset2 = 0;
            for ($j = 0; $j < $str2_len; $j++) {

                $grapheme2 = IntlString::grapheme($str2, $currentGraphemeOffset2, $nextGraphemeOffset2);

                $codepoint1 = \IntlChar::ord($grapheme1);
                $codepoint2 = \IntlChar::ord($grapheme2);

                if ($codepoint1 == $codepoint2 && abs($j - $i) <= $range) {
                    array_push($matches, \IntlChar::chr($codepoint1));
                    break;
                }

                $currentGraphemeOffset2 = $nextGraphemeOffset2;
            }
        }

        return $matches;
    }

    // gets the # of transpositions between two array of matching codepoints
    private static function transpositions($cp_array1, $cp_array2)
    {
        $count = 0;
        $min = min(count($cp_array1), count($cp_array2));

        for ($i = 0; $i < $min; $i++) {

            if ($cp_array1[$i] != $cp_array2[$i])
                ++$count;
        }

        return $count / 2;
    }

    // getst the prefix length to use
    public static function prefix_len($str1, $str1_len, $str2, $str2_len, $default_len = 4)
    {
        $min = min([$str1_len, $str2_len, $default_len]);
        $result = 0;

        $nextGraphemeOffset1 = 0;
        $nextGraphemeOffset2 = 0;
        for ($i = 0; $i < $min; $i++, $result++) {
            $currentGraphemeOffset1 = $nextGraphemeOffset1;
            $currentGraphemeOffset2 = $nextGraphemeOffset2;

            $grapheme1 = IntlString::grapheme($str1, $currentGraphemeOffset1, $nextGraphemeOffset1);
            $grapheme2 = IntlString::grapheme($str2, $currentGraphemeOffset2, $nextGraphemeOffset2);

            $codepoint1 = \IntlChar::ord($grapheme1);
            $codepoint2 = \IntlChar::ord($grapheme2);

            if ($codepoint1 != $codepoint2)
                break;
        }

        return $result;
    }

    /**
     *  Calculates the Jaro-Winkler distance between $str1 and $str2.
     *  The scaling factor will only be applied if the Jaro distance meets the threshold.
     *
     *  @param string $str1 A string.
     *  @param string $str2 A string.
     *  @param float $threshold The threshold to use.
     *  @param float $scaling_factor The scaling factor to use.
     *  @return float A number between 0 and 1. 0 Indicates no match. 1 indicates a perfect match.
     */
    public static function distance($str1, $str2, $threshold = 0.7, $scaling_factor = 0.1)
    {
        // convert both strings to utf8
        $str1_utf8 = IntlString::convert($str1);
        $str2_utf8 = IntlString::convert($str2);

        $str1_len = IntlString::strlen($str1_utf8);
        $str2_len = IntlString::strlen($str2_utf8);

        // check if the strings are equal
        if (IntlString::strcmp($str1_utf8, $str2_utf8) == 0)
            return 1.0;

        // get the max range the distance between to code points can be
        $range = JaroWinkler::max_range($str1_len, $str2_len);

        // get the matching code points
        $cp1_matches = JaroWinkler::matching_cp($str1_utf8, $str1_len, $str2_utf8, $str2_len, $range);
        $cp2_matches = JaroWinkler::matching_cp($str2_utf8, $str2_len, $str1_utf8, $str1_len, $range);

        // were there no matches?
        if (count($cp1_matches) == 0 || count($cp2_matches) == 0)
            return 0.0;

        // calculate the # of transpositions
        $transpositions = JaroWinkler::transpositions($cp1_matches, $cp2_matches);

        // calculate Jaro distance
        $jaro_distance = ((count($cp1_matches)/$str1_len) +
                          (count($cp1_matches)/$str2_len) +
                          ((count($cp1_matches) - $transpositions)/count($cp1_matches))) / 3.0;

        $jaro_winkler_distance = $jaro_distance;
        // only apply the prefix bonus if the jaro distance meets the threshold
        if ($jaro_distance > $threshold) {
            $prefix_len = JaroWinkler::prefix_len($str1_utf8, $str1_len, $str2_utf8, $str2_len);
            $jaro_winkler_distance += (($prefix_len * $scaling_factor) * (1.0 - $jaro_distance));
        }

        return $jaro_winkler_distance;
    }
}
