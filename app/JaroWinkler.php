<?php

namespace App;

use \App\Intl_UTF8;

class JaroWinkler {

	// gets the maximum range two matching codepoints are allowed to be apart 
	private static function max_range($str1_len, $str2_len) {

		return (int)(floor(max($str1_len, $str2_len) / 2)) - 1;
	}

	// gets the matching codepoints in an array
	private static function matching_cp($str1, $str1_len, $str2, $str2_len, $range) {

		$matches = [];
		for ($i = 0, $current_1 = 0, $next_1 = 0; $i < $str1_len; $i++) {


			$g_1 = Intl_UTF8::grapheme($str1, ($current_1 = $next_1), $next_1);
			$cp_1 = \IntlChar::ord($g_1);

			for ($j = 0, $current_2 = 0, $next_2 = 0; $j < $range; $j++) {

				$g_2 = Intl_UTF8::grapheme($str2, ($current_2 = $current_1 + $next_2), $next_2);
				$cp_2 = \IntlChar::ord($g_2);

				// make sure we're within range
				if ($j <= $range) {

					if ($cp_1 == $cp_2)
						array_push($matches, $cp_1);

					break;
				}
			}
		}

		return $matches;
	}

	// gets the # of transpositions between two array of matching codepoints
	private static function transpositions($cp_array1, $cp_array2) {

		$count = 0;
		$min = min(count($cp_array1), count($cp_array2));

		for ($i = 0; $i < $min; $i++) {

			if ($cp_array1[$i] != $cp_array2[$i])
				++$count;
		}

		return $count;
	}

	// getst the prefix length to use
	private static function prefix_len($str1, $str1_len, $str2, $str2_len, $default_len = 4) {

		$min = min([$str1_len, $str2_len, $default_len]);

		for ($i = 0, $current_1 = 0, $current_2 = 0, $next_1 = 0, $next_2 = 0; $i < $min; $i++) {

			$g_1 = Intl_UTF8::grapheme($str1, ($current_1 = $next_1), $next_1);
			$g_2 = Intl_UTF8::grapheme($str2, ($current_2 = $current_1 + $next_2), $next_2);
			$cp_1 = \IntlChar::ord($g_1);
			$cp_2 = \IntlChar::ord($g_2);

			if ($cp_1 != $cp_2)
				return $i;
		}

		return $min;
	}

	/*
	 * 	Calculates the Jaro-Winkler distance between $str1 and $str2.
	 *	The scaling factor will only be applied if the Jaro distance meets the threshold.
	 *
	 *	@param $str1 A string.
	 *	@param $str2 A string.
	 *	@param $threshold The threshold to use.
	 *	@param $scaling_factor The scaling factor to use.
	 *	@return A number between 0 and 1. 0 Indicates no match. 1 indicates a perfect match.
	 */
	public static function distance($str1, $str2, $threshold = 0.7, $scaling_factor = 0.1) {

		// convert both strings to utf8
		$str1_utf8 = Intl_UTF8::convert($str1);
		$str2_utf8 = Intl_UTF8::convert($str2);

		$str1_len = Intl_UTF8::strlen($str1_utf8);
		$str2_len = Intl_UTF8::strlen($str2_utf8);

		// check if the strings are equal
		if (Intl_UTF8::strcmp($str1_utf8, $str2_utf8) == 0)
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
		$transpositions = JaroWinkler::transpositions($cp1_matches, $cp2_matches) / 2.0;

		// calculate Jaro distance
		$jaro_distance = ((count($cp1_matches)/$str1_len) +
		                  (count($cp2_matches)/$str2_len) +
		                  ((count($cp1_matches) - $transpositions)/count($cp1_matches))) / 3.0;

		// calculate Jaro-Winkler distance
		// only apply the prefix bonus if the jaro distance meets the threshold
		if ($jaro_distance < $threshold) {

			$jaro_winkler_distance = $jaro_distance;
		} else {

			$prefix_len = JaroWinkler::prefix_len($str1_utf8, $str1_len, $str2_utf8, $str2_len);
			$jaro_winkler_distance = $jaro_distance + (($prefix_len * $scaling_factor) * (1 - $jaro_distance));
		}

		return $jaro_winkler_distance;
	}
}