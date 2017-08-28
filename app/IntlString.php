<?php

namespace App;

# requirements: php-intl

class IntlString {

	// converts a string to another encoding
	public static function convert($str1, $encoding = 'UTF-8') {

		$converter = new \UConverter($encoding);
		
		return $converter->convert($str1);
	}

	// gets the grapheme length of $str1
	// $str1 must be a UTF-8 string
	public static function strlen($str1) {

		return grapheme_strlen($str1);
	}

	public static function grapheme(string $str1, int $index, int &$next) {

		return grapheme_extract($str1, 1, GRAPHEME_EXTR_COUNT, $index, $next);
	}

	// compares two strings.
	public static function strcmp($str1, $str2) {

		$str1_length = IntlString::strlen($str1);
		$str2_length = IntlString::strlen($str2);

		// do basic length comparison
		if ($str1_length < $str2_length)
			return -1;
		else if ($str1_length > $str2_length)
			return 1;

		// get the the most grapheme units we can check against
		$min = min($str1_length, $str2_length);

		// iterate until $current_1 and $current_2 reach the $min grapheme length
		for ($i = 0, $current_1 = 0, $current_2 = 0, $next_1 = 0, $next_2 = 0;
			 $i < $min; $i++) {

			// get the grapheme for the current pos and advance $current_x to $next_x
			// this will let us get the next grapheme on the next call
			$g_1 = IntlString::grapheme($str1, ($current_1 = $next_1), $next_1);
			$g_2 = IntlString::grapheme($str2, ($current_2 = $next_2), $next_2);

			// get the code point for the grapheme for comparison
			$cp_1 = \IntlChar::ord($g_1);
			$cp_2 = \IntlChar::ord($g_2);

			if ($cp_1 < $cp_2)
				return -1;
			elseif ($cp_1 > $cp_2)
				return 1;
		}

		// strings are equal if execution reaches here
		return 0;
	}
}