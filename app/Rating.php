<?php

namespace App;

// https://www.evanmiller.org/how-not-to-sort-by-average-rating.html
class Rating
{
    /**
     * Gets the average rating for a manga.
     *
     * @param Manga $manga
     * @return int|false
     */
    public static function average(Manga $manga)
    {
        return $manga->votes->count() > 0 ?
            $manga->votes->average('rating') :
            false;
    }

    /**
     * Gets the lower bound Wilson score for a manga.
     *
     * @param Manga $manga
     * @return float|false
     */
    public static function get(Manga $manga)
    {
        if ($manga->votes->count() == 0)
            return false;

        $positiveRatings = $manga->votes->where('rating', '>', '50')->count();
        $totalRatings = $manga->votes->count();

        return self::getScore($positiveRatings, $totalRatings);
    }

    /**
     * Gets the lower bound Wilson score confidence interval for a Bernoulli parameter.
     *
     * @param int $positiveRatings
     * @param int $totalRatings
     * @return float|false
     */
    public static function getScore(int $positiveRatings, int $totalRatings)
    {
        if ($totalRatings <= 0 || $positiveRatings < 0)
            return false;

        $z = self::z();
        $phat = 1 * $positiveRatings / $totalRatings;

        return ($phat + $z*$z/(2*$totalRatings) -
                $z * sqrt(($phat*(1-$phat)+$z*$z/(4*$totalRatings))/$totalRatings))/(1+$z*$z/$totalRatings);
    }

    private static function z()
    {
        static $z;

        if (empty($z))
            $z = Acklam::get(1 - (1 - 0.95) / 2);

        return $z;
    }
}