<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibraryPrivilege extends Model
{
    //
    protected $fillable = ['user_id', 'library_id'];

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getLibraryId()
    {
        return $this->library_id;
    }

    public static function getIds($user_id)
    {
        $ids = [];

        $privileges = LibraryPrivilege::where('user_id', '=', $user_id)->get();
        if ($privileges == null)
            return null;

        foreach ($privileges as $privilege) {
            array_push($ids, $privilege->getLibraryId());
        }

        return count($ids) > 0 ? $ids : null;
    }
}
