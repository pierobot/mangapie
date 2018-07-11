<?php

namespace App;

use \Symfony\Component\Finder\Finder;

class Scanner
{
    public static function removeExtension($str)
    {
        // extension garbage
        $pattern = "/\.\w+$/";

        return preg_replace($pattern, "", $str);
    }

    public static function removeParenthesis($str)
    {
        $pattern = "/[ _\-\.]*(\(.+\)|\[.+\])[ _\-\.]*/U";

        return preg_replace($pattern, "", $str);
    }

    public static function removeVolume($str)
    {
        $pattern = "/[ \.\_\-]*(v|vol(ume)?)[ \.\_\-]*\d+([ \.\_\-]\d+)?/i";

        return preg_replace($pattern, "", $str);
    }

    public static function replaceUnderscoreMultipleSpace($str)
    {
        /*
            do not replace periods as there are titles with them.
            examples:
                .hack//Sign
                .hack//Dusk
                .hack//Alcor
                .hack//Link
                .hack//Quantum+

            and there are probably lots more.
        */
        $pattern = "/_{1,}| {2,}/";

        return trim(preg_replace($pattern, " ", $str));
    }

    /**
     * Removes typical extra information from a name.
     * For example, "Three_Word_Title   [2003]__v01-38--(Digital)-(person-Group)" will become "Three Word Title".
     *
     * @param $name The name to clean.
     * @return string
     */
    public static function clean($name)
    {
        $result = self::removeExtension($name);
        $result = self::removeParenthesis($result);
        $result = self::removeVolume($result);
        $result = self::replaceUnderscoreMultipleSpace($result);

        return $result;
    }

    public static function getArchives($manga_id, $path, $sort = 'ascending')
    {
        try {
            // get all the files in the path and filter by archives
            $files = Finder::create()->in($path)
                ->name('*.zip')
                ->name('*.cbz')
                ->name('*.rar')
                ->name('*.cbr');
        } catch (\InvalidArgumentException $e) {
            return false;
        }

        // sort by number tokens
        $files->sort(function ($left, $right) use ($sort) {
            return $sort == 'ascending' ? strnatcasecmp($left->getFilename(), $right->getFilename()) :
                strnatcasecmp($right->getFilename(), $left->getFilename());
        });

        $archives = [];
        foreach ($files as $file) {
            $archive = [];
            $archive['manga_id'] = $manga_id;
            $archive['name'] = $file->getRelativePathname();
            $archive['size'] = $file->getSize();

            array_push($archives, $archive);
        }

        return $archives;
    }

    public static function scan(Library $library)
    {
        Jobs\ScanLibrary::dispatch($library);
    }
}
