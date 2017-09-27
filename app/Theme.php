<?php

namespace App;

use \Symfony\Component\Finder\Finder;

class Theme
{
    /**
     * Gets the path relative to the working directory of a theme.
     *
     *  @param string $name The name of the theme. (Ex: bootswatch/slate)
     *  @return string The path of the theme.
     */
    public static function path($name)
    {
        if (empty($name) === true)
            return false;

        return $theme_path = '/public/themes/' . $name . '/' . 'bootstrap.min.css';
    }
    /**
     *  Gets all the themes in the public/themes directory.
     *
     *  @return An array containing all the themes.
     *          The structure of the array will look like so:
     *              array:1 [▼
     *                "bootswatch" => array:17 [▼
     *                  "yeti" => "public/themes/bootswatch/yeti/bootstrap.min.css"
     *                  "darkly" => "public/themes/bootswatch/darkly/bootstrap.min.css"
     *                  "spacelab" => "public/themes/bootswatch/spacelab/bootstrap.min.css"
     *                  "paper" => "public/themes/bootswatch/paper/bootstrap.min.css"
     *                  "cosmo" => "public/themes/bootswatch/cosmo/bootstrap.min.css"
     *                  "lumen" => "public/themes/bootswatch/lumen/bootstrap.min.css"
     *                  "sandstone" => "public/themes/bootswatch/sandstone/bootstrap.min.css"
     *                  "journal" => "public/themes/bootswatch/journal/bootstrap.min.css"
     *                  "united" => "public/themes/bootswatch/united/bootstrap.min.css"
     *                  "readable" => "public/themes/bootswatch/readable/bootstrap.min.css"
     *                  "simplex" => "public/themes/bootswatch/simplex/bootstrap.min.css"
     *                  "custom" => "public/themes/bootswatch/custom/bootstrap.min.css"
     *                  "superhero" => "public/themes/bootswatch/superhero/bootstrap.min.css"
     *                  "flatly" => "public/themes/bootswatch/flatly/bootstrap.min.css"
     *                  "slate" => "public/themes/bootswatch/slate/bootstrap.min.css"
     *                  "cerulean" => "public/themes/bootswatch/cerulean/bootstrap.min.css"
     *                  "cyborg" => "public/themes/bootswatch/cyborg/bootstrap.min.css"
     *                ]
     *              ]
     */
    public static function all($include_path = true)
    {
        $theme_collections = [];
        $themes = [];

        $dirs = Finder::create()->in('public/themes')->depth('== 2')->directories();
        foreach ($dirs as $dir) {

            // ex: bootswatch
            $collection_name = $dir->getPathInfo()->getPathInfo()->getFilename();

            if (array_key_exists($collection_name, $theme_collections) !== true)
                $theme_collections[$collection_name] = [];

            $files = Finder::create()->in($dir->getPathInfo()->getPathInfo()->getPathname())
                        ->depth('== 1')
                        ->files()
                        ->name('*bootstrap.min.css');

            foreach ($files as $file) {

                // ex: default
                $theme_name = $file->getPathInfo()->getFilename();
                // ex: public/themes/bootswatch/default/bootstrap.min.css
                $theme_path = $file->getPathname();

                if ($include_path === true)
                    $theme_collections[$collection_name][$theme_name] = $theme_path;
                else
                    $theme_collections[$collection_name][$theme_name] = $theme_name;
            }
        }

        return $theme_collections;
    }
}
