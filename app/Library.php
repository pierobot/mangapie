<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Symfony\Component\Finder\Finder;
use \Carbon\Carbon;

use \App\Manga;

class Library extends Model
{
    //
    protected $fillable = ['name', 'path'];

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function manga()
    {
        return $this->hasMany('App\Manga', 'library_id', 'id');
    }

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

    public function getArchives($manga_id, $path, $sort = 'ascending')
    {
        // get all the files in the path and filter by archives
        $files = Finder::create()->in($path)
                                  ->name('*.zip')
                                  ->name('*.cbz')
                                  ->name('*.rar')
                                  ->name('*.cbr');

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

    public function scan()
    {
        // scan and add new directories
        foreach (\File::directories($this->getPath()) as $path) {
            $manga = Manga::updateOrCreate([
                'name' => self::clean(pathinfo($path, PATHINFO_BASENAME)),
                'path' => $path,
                'library_id' => Library::where('name','=',$this->getName())->first()->id
            ]);

            // scan for new archives
            $archives = self::getArchives($manga->getId(), $path);
            if (empty($archives) == true)
                continue;

            $names = [];
            foreach ($archives as $archive) {
                array_push($names, $archive['name']);
            }

            $allArchives = Archive::where('manga_id', $manga->getId())->get();
            // filter out the ones that are not present in the database
            $newArchives = collect($archives)->reject(function ($archive) use ($allArchives) {
                return $allArchives->where('name', $archive['name'])->first() != null;
            });

            // filter out the ones that still exist
            $removedArchives = $allArchives->reject(function ($archive) use ($names) {
                return in_array($archive->getName(), $names);
            });

            if ($removedArchives->count() > 0) {
                \Event::fire(new Events\Archive\RemovedArchives($removedArchives));
            }

            if ($newArchives->count() > 0) {
                \Event::fire(new Events\Archive\NewArchives($newArchives));
            }
        }

        // iterate through all the manga in the library
        // and remove those that no longer exist in the filesystem
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        foreach ($manga as $manga_) {
            if (\File::exists($manga_->getPath()) === false) {
                $manga_->forceDelete();
            }
        }
    }

    public function forceDelete()
    {
        // get all the manga that have library_id to ours
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        // and delete them
        foreach ($manga as $manga_) {
            // Manga::forceDelete deletes all the references to other tables (artists, authors, manga_information, etc..)
            $manga_->forceDelete();
        }

        parent::forceDelete();
    }
}
