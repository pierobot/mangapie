<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReaderSettingsRequest;
use App\ReaderSettings;

class ReaderSettingsController extends Controller
{
    public function put(ReaderSettingsRequest $request)
    {
        $user = $request->user();
        $direction = $request->get('direction');
        $mangaId = $request->get('manga_id');

        ReaderSettings::updateOrCreate([
            'user_id' => $user->id,
            'manga_id' => $mangaId
        ], [
            'direction' => $direction
        ]);

        return redirect()->back();
    }
}
