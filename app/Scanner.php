<?php

namespace App;

use \Symfony\Component\Finder\Finder;

class Scanner
{
    public static function removeExtension(string $str)
    {
        // extension garbage
        $pattern = "/\.\w+$/";

        return preg_replace($pattern, "", $str);
    }

    public static function removeParenthesis(string $str)
    {
        $pattern = "/[ _\-\.]*(\(.+\)|\[.+\])[ _\-\.]*/U";

        return preg_replace($pattern, "", $str);
    }

    public static function removeVolume(string $str)
    {
        $pattern = "/[ \.\_\-]*(v|vol(ume)?)[ \.\_\-]*\d+([ \.\_\-]\d+)?/i";

        return preg_replace($pattern, "", $str);
    }

    public static function replaceUnderscoreMultipleSpace(string $str)
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
     * @param string $name The name to clean.
     * @return string
     */
    public static function clean(string $name)
    {
        $result = self::removeExtension($name);
        $result = self::removeParenthesis($result);
        $result = self::removeVolume($result);
        $result = self::replaceUnderscoreMultipleSpace($result);

        return $result;
    }

    public static function getArchives(int $manga_id, string $path, string $sort = 'ascending')
    {
        try {
            // get all the files in the path and filter by archives
            $files = Finder::create()->in($path)
                ->name('/\.zip$/i')
                ->name('/\.cbz$/i')
                ->name('/\.rar$/i')
                ->name('/\.cbr$/i');
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

    /**
     * Matches and returns an array that holds the volume and/or chapter strings.
     *
     * @param string $str
     * @return array|bool
     */
    public static function getVolumesAndChapters(string $str)
    {
        $pattern = "/((v|vol(ume)?)|(c|ch(apter)?)|(omnibus?))[ \.\_\-]*\d+([ \.\_\-]\d+)?/i";
        $matches = [];

        /* Using PREG_PATTERN_ORDER will return all the matches we need in the first array index.
         *
         * Example: 'Soredemo Machi wa Mawatteiru - c122-130 (v16) (end) [CR].zip' will return ["c122-130", "v16"]
         */
        $matchResult = preg_match_all($pattern, $str, $matches, PREG_PATTERN_ORDER);

        return $matchResult !== false ? $matches[0] : false;
    }

    /**
     * Simplifies the given name into volumes and chapters.
     *
     * @param string $name
     * @return string
     */
    public static function simplifyName(string $name)
    {
        $volCh = self::getVolumesAndChapters($name);
        // If there is no volume or chapter in the name, or if the parsing failed
        // then just use the archive name :shrug:
        if (empty($volCh)) {
            $nameVolCh = $name;
        } else {
            $nameVolCh = '';
            foreach ($volCh as $part) {
                $nameVolCh .= $part . ' ';
            }
        }

        return $nameVolCh;
    }
}
