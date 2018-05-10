<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        $pattern = "/[ _\-\.]*(\((.+)\)|\[(.+)\])[ _\-\.]*/";

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

        return preg_replace($pattern, " ", $str);
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

    public function scan()
    {
        // scan and add new directories
        foreach (\File::directories($this->getPath()) as $path) {
            $manga = Manga::updateOrCreate([
                'name' => self::clean(pathinfo($path, PATHINFO_BASENAME)),
                'path' => $path,
                'library_id' => Library::where('name','=',$this->getName())->first()->id
            ]);
        }

        // iterate through all the manga in the library
        // and remove those that no longer exist in the filesystem
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        foreach ($manga as $manga_) {
            if (\File::exists($manga_->getPath()) === false) {
                $manga_->forceDelete();
            }
        }

        // refresh the collection
        $manga = Manga::where('library_id', '=', $this->getId())->get();
        foreach ($manga as $manga_) {
            // skip if there is already information or set to ignore
            if ($manga_->getMangaUpdatesId() != null || $manga_->getIgnoreOnScan() == true)
                continue;

            MangaUpdates::autofill($manga_);
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
