<?php

namespace App;

// https://stackedboxes.org/2017/05/01/acklams-normal-quantile-function/
class Acklam
{
    // Coefficients in rational approximations.
    private const a = [
        -3.969683028665376e+01,
        2.209460984245205e+02,
        -2.759285104469687e+02,
        1.383577518672690e+02,
        -3.066479806614716e+01,
        2.506628277459239e+00
    ];

    private const b = [
        -5.447609879822406e+01,
        1.615858368580409e+02,
        -1.556989798598866e+02,
        6.680131188771972e+01,
        -1.328068155288572e+01
    ];

    private const c = [
        -7.784894002430293e-03,
        -3.223964580411365e-01,
        -2.400758277161838e+00,
        -2.549732539343734e+00,
        4.374664141464968e+00,
        2.938163982698783e+00
    ];

    private const d = [
        7.784695709041462e-03,
        3.224671290700398e-01,
        2.445134137142996e+00,
        3.754408661907416e+00
    ];

    // breakpoints
    private const p_low = 0.02425;
    private const p_high = 1 - self::p_low;

    /**
     * Gets the inverse normal cumulative distribution for the given value.
     * @param $p
     * @return float|false
     */
    public static function get($p)
    {
        $x = false;

        if (0 < $p && $p < self::p_low) {
            // rational approximation for lower region
            $q = sqrt(-2 * log($p));
            $x = (((((self::c[0]*$q+self::c[1])*$q+self::c[2])*$q+self::c[3])*$q+self::c[4])*$q+self::c[5]) /
                ((((self::d[0]*$q+self::d[1])*$q+self::d[2])*$q+self::d[3])*$q+1);
        } elseif (self::p_low <= $p && $p <= self::p_high) {
            // rational approximation for central region
            $q = $p - 0.5;
            $r = $q*$q;
            $x = (((((self::a[0]*$r+self::a[1])*$r+self::a[2])*$r+self::a[3])*$r+self::a[4])*$r+self::a[5])*$q /
                (((((self::b[0]*$r+self::b[1])*$r+self::b[2])*$r+self::b[3])*$r+self::b[4])*$r+1);
        } elseif (self::p_high < $p && $p < 1) {
            // rational approximation for upper region
            $q = sqrt(-2 * log(1 - $p));
            $x = -(((((self::c[0]*$q+self::c[1])*$q+self::c[2])*$q+self::c[3])*$q+self::c[4])*$q+self::c[5]) /
                ((((self::c[0]*$q+self::c[1])*$q+self::c[2])*$q+self::c[3])*$q+1);
        }

        return $x;
    }
}
