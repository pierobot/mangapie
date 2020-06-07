<?php

namespace App\Http\Controllers;

use App\Archive;
use App\ImageArchive;
use App\Manga;

use App\ReaderHistory;
use App\Scanner;
use Illuminate\Http\Request;

class ReaderHistoryController extends Controller
{
    /**
     * Route to mark the given archive as read.
     *
     * @param Archive $archive
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markArchiveRead(Archive $archive)
    {
        $user = \Auth::user();
        $archive = $archive->loadMissing(['manga:id,path']);
        $name = Scanner::simplifyName(Scanner::removeExtension($archive->name));

        $count = $archive->getPageCount();

        $user->readerHistory()->updateOrCreate(
            [
                'archive_id' => $archive->id
            ],
            [
                'page' => $count,
                'page_count' => $count
            ]
        );

        return redirect()->back()->with('success', "Marked \"$name\" as read.");
    }

    /**
     * Route to mark the given archive as unread.
     *
     * @param Archive $archive
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markArchiveUnread(Archive $archive)
    {
        $user = \Auth::user();
        $name = Scanner::simplifyName(Scanner::removeExtension($archive->name));

        $user->readerHistory()->where('archive_id', $archive->id)->forceDelete();

        return redirect()->back()->with('success', "Marked \"$name\" as unread.");
    }

    /**
     * Route to mark all archives in a series using a filter as read.
     *
     * Note that this will not mark the series as completed because it may
     * still be ongoing or have archives missing.
     *
     * @param Manga $manga
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function markAllArchivesRead(Manga $manga)
    {
        $filter = request()->get('filter', 'Root');
        $user = \Auth::user();

        $archives = $manga->archives()->get();

        $archives = $archives->filter(function (Archive $archive) use ($filter) {
            $fileInfo = new \SplFileInfo($archive->name);
            $basePath = $fileInfo->getPath();

            return $basePath == $filter;
        });

        $counts = [];
        foreach ($archives as $archive) {
            $counts []= $archive->getPageCount();
        }

        \DB::transaction(function () use ($manga, $user, $archives, $counts) {
            /** @var Archive $archive */
            foreach ($archives as $index => $archive) {
                $user->readerHistory()->updateOrCreate(
                    [
                        'manga_id' => $manga->id,
                        'archive_id' => $archive->id,
                    ],
                    [
                        'page' => $counts[$index],
                        'page_count' => $counts[$index]
                    ]
                );
            }
        });

        return redirect()->back()->with('success', "Marked all archives in \"$filter\" as read.");
    }
}
