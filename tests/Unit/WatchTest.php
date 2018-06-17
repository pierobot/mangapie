<?php

namespace Tests\Unit;

use App\WatchDescriptor;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

use App\Library;
use App\Watcher;

use App\Console\Commands\Watch;

/**
 * Tests typical usage of \App\Watch.
 * PHPUnit has no way to execute tests in order, so we have to write big tests. *shrug*
 *
 * @requires OS Linux
 * @requires extension inotify
 *
 * @covers \App\Watcher
 * @covers \App\ArchiveDescriptor
 * @covers \App\DirectoryDescriptor
 * @covers \App\WatchDescriptor
 *
 * @covers \App\Listeners\ArchiveEventSubscriber
 * @covers \App\Listeners\DirectoryEventSubscriber
 *
 * @covers \App\Observers\MangaObserver
 */
class WatchTest extends TestCase
{
    use RefreshDatabase;

    private $names1 = [
        [
            "name" => "Food Wars!",
            "archives" => [
                "Food Wars! v01 (2014) (Digital) (LuCaZ).cbz",
                "Food Wars! v02 (2014) (Digital) (LuCaZ).cbz",
                "Food Wars! v03 (2014) (Digital) (LuCaZ).cbz",
                "Food Wars! v04 (2015) (Digital) (LuCaZ).cbz",
                "Food Wars! v05 (2015) (Digital) (LuCaZ).cbz"
            ]
        ],

        [
            "name" => "Maison Ikkoku",
            "archives"=> [
                "Maison_Ikkoku_v01[MangaProject].rar",
                "Maison_Ikkoku_v02[MangaProject].rar",
                "Maison_Ikkoku_v03[MangaProject].rar",
                "Maison_Ikkoku_v04[MangaProject].rar",
                "Maison_Ikkoku_v05[MangaProject].rar"
            ],
        ],

        [
            "name" => "Inuyasha",
            "archives" => [
                "Inuyasha_v01.rar",
                "Inuyasha_v02.rar",
                "Inuyasha_v03.rar"
            ]
        ],
    ];

    private $names2 = [
        [
            "name" => "Berserk",
            "subdir" => "Berserk (Digital) (danke-Empire)",
            "archives" => [
                "Berserk v01 (2003) (Digital) (danke-Empire).cbz",
                "Berserk v02 (2004) (Digital) (danke-Empire).cbz",
                "Berserk v03 (2004) (Digital) (danke-Empire).cbz",
            ]
        ],

        [
            "name" => "Golden Kamuy",
            "subdir" => "Golden Kamuy (Digital) (danke-Empire)",
            "archives" => [
                "Golden Kamuy v01 (2017) (Digital) (danke-Empire).cbz",
                "Golden Kamuy v02 (2017) (Digital) (danke-Empire).cbz",
                "Golden Kamuy v03 (2017) (Digital) (danke-Empire).cbz",
                "Golden Kamuy v04 (2018) (Digital) (danke-Empire).cbz"
            ]
        ],
    ];

    private $names3 = [
        [
            "sourceName" => "The Legend of a Vampire Hunter",
            "destinationName" => "Hellsing",
            "archives" => [
                "Hellsing v01 [Dark Horse].rar",
                "Hellsing v02 [Dark Horse].rar",
                "Hellsing v03 [Dark Horse].rar",
            ]
        ],

        [
            "sourceName" => "Mahoutskai no Yome",
            "destinationName" => "The Ancient Magus' Bride",
            "archives" => [
                "The Ancient Magus' Bride v01 (2015) (Digital) (danke-Empire).cbz",
                "The Ancient Magus' Bride v02 (2015) (Digital) (danke-Empire).cbz",
                "The Ancient Magus' Bride v03 (2015) (Digital) (danke-Empire).cbz",
            ]
        ],

        [
            "sourceName" => "Inugamihime ni Kuchizuke",
            "destinationName" => "Be My Dog",
            "archives" => [
                "[Antisense]Be My Dog v01.zip",
                "[Antisense]Be My Dog v02.zip",
                "[Antisense]Be My Dog v03.zip",
            ]
        ],
    ];

    private $names4 = [
        [
            "sourceName" => "Kangoku Gakuen",
            "destinationName" => "Prison School",
            "subdir" => "Kangoku Gakuen (Prison School)",
            "archives" => [
                "Kangoku Gakuen (Prison School) - c001-008 (v01) [EMS].zip",
                "Kangoku Gakuen (Prison School) - c009-018 (v02) [EMS][Pinoymanga].zip",
                "Kangoku Gakuen (Prison School) - c019-028 (v03) [Pinoymanga][LAS].zip"
            ]
        ],

        [
            "sourceName" => "xxxHOLIC",
            "destinationName" => "xxxHoLic",
            "subdir" => "xxxHOLIC",
            "archives" => [
                "xxxHOLIC Omnibus 01.zip",
                "xxxHOLIC Omnibus 02.zip",
            ]
        ],
    ];

    private $names5 = [
        [
            "sourceName" => "ACCA",
            "destinationName" => "ACCA - Inspectors of the 13 States",
            "archives" => [
                "ACCA v01 [Uasaha] (Yen Press).zip",
                "ACCA v02 [Uasaha] (Yen Press).zip",
            ]
        ],

        [
            "sourceName" => "D-FRAGMENTS",
            "destinationName" => "D-Frag!",
            "archives" => [
                "D-FRAG v01 [Uasaha] (Seven Seas).zip",
                "D-FRAG v02 [Uasaha] (Seven Seas).zip",
                "D-FRAG v03 [Uasaha] (Seven Seas).zip",
            ]
        ],
    ];

    protected static $root1;
    protected static $root2;
    protected static $root3;
    protected static $root4;
    protected static $root5;

    public static function setUpBeforeClass()
    {
        self::$root1 = getcwd() . DIRECTORY_SEPARATOR . "tests/Data/Libraries/manga1";
        self::$root2 = getcwd() . DIRECTORY_SEPARATOR . "tests/Data/Libraries/manga2";
        self::$root3 = getcwd() . DIRECTORY_SEPARATOR . "tests/Data/Libraries/manga3";
        self::$root4 = getcwd() . DIRECTORY_SEPARATOR . "tests/Data/Libraries/manga4";
        self::$root5 = getcwd() . DIRECTORY_SEPARATOR . "tests/Data/Libraries/manga5";
    }

    public function setUp()
    {
        parent::setUp();

        // start eating the echo output from App\Watcher
        ob_start();

        if (\App\Library::count() !== 0)
            return;

        $this->app->bind(\App\Observers\ArchiveObserver::class, function () {
            return $this->getMockBuilder(\App\Observers\ArchiveObserver::class)->disableOriginalConstructor()->getMock();
        });

        \App\Library::create([
            'name' => 'manga1',
            'path' => self::$root1
        ]);

        \App\Library::create([
            'name' => 'manga2',
            'path' => self::$root2
        ]);

        \App\Library::create([
            'name' => 'manga3',
            'path' => self::$root3
        ]);

        \App\Library::create([
            'name' => 'manga4',
            'path' => self::$root4 . DIRECTORY_SEPARATOR . 'dest'
        ]);

        \App\Library::create([
            'name' => 'manga5',
            'path' => self::$root5 . DIRECTORY_SEPARATOR . 'dest'
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();

        // stop eating the output from App\Watcher
        ob_end_clean();
    }

    public function testTrackEmptyPathReturnsFalse()
    {
        $watch = new Watcher();
        $this->assertFalse($watch->track(""));
    }

    public function testInvalidPathReturnsFalse()
    {
        $watch = new Watcher();
        $this->assertFalse($watch->track("/a/b/c/d/asd/this/probably/does/not/exist"));
    }

    public function testDirectoryDescriptor()
    {
        $descriptor = WatchDescriptor::make(2, 1, true, '/a/b/c', false);

        $this->assertTrue($descriptor->getWd() == 2);
        $this->assertTrue($descriptor->getParent() == 1);
        $this->assertTrue($descriptor->isDirectory());
        $this->assertFalse($descriptor->isArchive());
        $this->assertTrue($descriptor->getPath() === '/a/b/c');
        $this->assertFalse($descriptor->isSymbolicLink());
    }

    public function testArchiveDescriptor()
    {
        $descriptor = WatchDescriptor::make(2, 1, false, '/a/b/c', false);

        $this->assertTrue($descriptor->getWd() == 2);
        $this->assertTrue($descriptor->getParent() == 1);
        $this->assertFalse($descriptor->isDirectory());
        $this->assertTrue($descriptor->isArchive());
        $this->assertTrue($descriptor->getPath() === '/a/b/c');
        $this->assertFalse($descriptor->isSymbolicLink());
    }

    public function testCreateAndRemoveDirectories()
    {
        $root = self::$root1;

        $watch = new Watcher();
        $watch->track($root);

        foreach ($this->names1 as $item) {
            $name = $item["name"];
            $path = $root . DIRECTORY_SEPARATOR . $name;

            if (file_exists($path))
                continue;

            mkdir($path);

            $watch->go(true);

            $this->assertDatabaseHas('manga', [
                'name' => $name,
                'path' => $path,
            ]);

            foreach ($item['archives'] as $archive) {
                $archivePath = $path . DIRECTORY_SEPARATOR . $archive;
                file_put_contents($archivePath, 'asd');

                $watch->go(true); // create

                $this->assertDatabaseHas('archives', [
                    'name' => $archive,
                    'size' => 3
                ]);

                unlink($archivePath);

                $watch->go(true);

                $this->assertDatabaseMissing('archives', [
                    'name' => $archive,
                    'size' => 3
                ]);
            }

            shell_exec("rm -rf tests/Data/Libraries/manga1/" . escapeshellarg($name));

            $watch->go(true);

            $this->assertDatabaseMissing('manga', [
                'name' => $name,
                'path' => $path,
            ]);
        }
    }

    public function testCreateAndRemoveNestedDirectories()
    {
        $root = self::$root2;

        $watch = new Watcher();
        $watch->track($root);

        foreach ($this->names2 as $item) {
            $name = $item["name"];
            $subdir = $item["subdir"];
            $path = $root . DIRECTORY_SEPARATOR . $name;

            if (file_exists($path))
                continue;

            mkdir($path);

            \App\Manga::create([
                'name' => $name,
                'path' => $path,
                'library_id' => 2
            ]);

            $path = $path . DIRECTORY_SEPARATOR . $subdir;
            mkdir($path);
        }

        $watch = new Watcher();
        $watch->track($root, [], false, true);

        foreach ($this->names2 as $item) {
            $name = $item["name"];
            $subdir = $item["subdir"];
            $path = self::$root2 . DIRECTORY_SEPARATOR . $name;

            foreach ($item['archives'] as $archive) {
                $archivePath = $path . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $archive;
                file_put_contents($archivePath, 'asd');

                $watch->go(true);

                $this->assertDatabaseHas('archives', [
                    'name' => $subdir . DIRECTORY_SEPARATOR . $archive,
                    'size' => 3
                ]);
            }

            shell_exec("rm -rf tests/Data/Libraries/manga2/" . escapeshellarg($name));

            $watch->go(true);

            $this->assertDatabaseMissing('manga', [
                'name' => $name,
                'path' => $path,
            ]);
        }
    }

    public function testMoveDirectories()
    {
        $root = self::$root3;

        $watch = new Watcher();
        $watch->track($root);

        foreach ($this->names3 as $item) {
            $sourceName = $item['sourceName'];
            $destinationName = $item['destinationName'];
            $sourcePath = $root . DIRECTORY_SEPARATOR . $sourceName;
            $destinationPath = $root . DIRECTORY_SEPARATOR . $destinationName;

            mkdir($sourcePath);

            $watch->go(true);

            $this->assertDatabaseHas('manga', [
                'name' => $sourceName,
                'path' => $sourcePath,
            ]);

            $sourceId = \App\Manga::where('name', $sourceName)->first()->getId();

            foreach ($item['archives'] as $archive) {
                $archiveSourcePath = $sourcePath . DIRECTORY_SEPARATOR . $archive;
                file_put_contents($archiveSourcePath, 'asd');

                $watch->go(true);

                $this->assertDatabaseHas('archives', [
                    'manga_id' => $sourceId,
                    'name' => $archive,
                    'size' => 3
                ]);
            }

            rename($sourcePath, $destinationPath);

            $watch->go(true);

            $this->assertDatabaseHas('manga', [
                'name' => $destinationName,
                'path' => $destinationPath,
            ]);

            $destinationId = \App\Manga::where('name', $destinationName)->first()->getId();

            foreach ($item['archives'] as $archive) {
                $this->assertDatabaseMissing('archives', [
                    'manga_id' => $sourceId,
                    'name' => $archive,
                    'size' => 3
                ]);

                $this->assertDatabaseHas('archives', [
                    'manga_id' => $destinationId,
                    'name' => $archive,
                    'size' => 3
                ]);
            }

            $this->assertDatabaseMissing('manga', [
                'name' => $sourceName,
                'path' => $sourcePath,
            ]);

            shell_exec("rm -rf tests/Data/Libraries/manga3/" . escapeshellarg($destinationName));

            $watch->go(true);

            $this->assertDatabaseMissing('manga', [
                'name' => $destinationName,
                'path' => $destinationPath,
            ]);
        }
    }

    public function testCreateAndRemoveSymbolicLinkToDirectories()
    {
        $root = self::$root4;
        $sourceRoot = $root . DIRECTORY_SEPARATOR . 'src';
        $destinationRoot = $root . DIRECTORY_SEPARATOR . 'dest';

        $watch = new Watcher();
        $watch->track($destinationRoot);

        foreach ($this->names4 as $item) {
            $sourceName = $item['sourceName'];
            $destinationName = $item['destinationName'];
            $subdir = $item['subdir'];
            $sourcePath = $sourceRoot . DIRECTORY_SEPARATOR . $sourceName;
            $destinationPath = $destinationRoot . DIRECTORY_SEPARATOR . $destinationName;

            mkdir($sourcePath);

            mkdir($sourcePath . DIRECTORY_SEPARATOR . $subdir);

            foreach ($item['archives'] as $archive) {
                $archiveSourcePath = $sourcePath . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $archive;
                file_put_contents($archiveSourcePath, 'asd');
            }

            symlink($sourcePath, $destinationPath);

            $watch->go(true);

            $this->assertDatabaseHas('manga', [
                'name' => $destinationName,
                'path' => $destinationPath
            ]);

            foreach ($item['archives'] as $archive) {
                $sourceArchivePath = $sourcePath . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $archive;
                $destinationArchivePath = $destinationPath . DIRECTORY_SEPARATOR . $subdir . DIRECTORY_SEPARATOR . $archive;

                $this->assertDatabaseHas('archives', [
                    'name' => $subdir . DIRECTORY_SEPARATOR . $archive,
                    'size' => 3
                ]);

                unlink($destinationArchivePath);

                $watch->go(true);

                $this->assertDatabaseMissing('archives', [
                    'name' => $subdir . DIRECTORY_SEPARATOR . $archive,
                    'size' => 3
                ]);
            }

            unlink($destinationPath);

            $watch->go(true);

            $this->assertDatabaseMissing('manga', [
                'name' => $destinationName,
                'path' => $destinationPath
            ]);

            shell_exec("rm -rf tests/Data/Libraries/manga4/src/" . escapeshellarg($sourceName));
        }
    }

    public function testCreateAndRemoveSymbolicLinkInDirectories()
    {
        $root = self::$root5;
        $sourceRoot = $root . DIRECTORY_SEPARATOR . 'src';
        $destinationRoot = $root . DIRECTORY_SEPARATOR . 'dest';

        $watch = new Watcher();
        $watch->track($destinationRoot);

        foreach ($this->names5 as $item) {
            $sourceName = $item['sourceName'];
            $destinationName = $item['destinationName'];
            $sourcePath = $sourceRoot . DIRECTORY_SEPARATOR . $sourceName;
            $destinationPath = $destinationRoot . DIRECTORY_SEPARATOR . $destinationName;

            mkdir($sourcePath);

            foreach ($item['archives'] as $archive) {
                $archiveSourcePath = $sourcePath . DIRECTORY_SEPARATOR . $archive;
                file_put_contents($archiveSourcePath, 'asd');
            }

            mkdir($destinationPath);

            $watch->go(true);

            $this->assertDatabaseHas('manga', [
                'name' => $destinationName,
                'path' => $destinationPath
            ]);

            $destinationPath .= DIRECTORY_SEPARATOR . $destinationName;

            symlink($sourcePath, $destinationPath);

            $watch->go(true);

            foreach ($item['archives'] as $archive) {
                $destinationArchivePath = $destinationPath . DIRECTORY_SEPARATOR . $archive;

                $this->assertDatabaseHas('archives', [
                    'name' => $destinationName . DIRECTORY_SEPARATOR . $archive,
                    'size' => 3
                ]);
            }

            unlink($destinationPath);

            $watch->go(true);

            foreach ($item['archives'] as $archive) {
                $destinationArchivePath = $destinationPath . DIRECTORY_SEPARATOR . $archive;

                $this->assertDatabaseMissing('archives', [
                    'name' => $destinationName . DIRECTORY_SEPARATOR . $archive,
                    'size' => 3
                ]);
            }

            $this->assertDatabaseHas('manga', [
                'name' => $destinationName,
                'path' => $destinationRoot . DIRECTORY_SEPARATOR . $destinationName
            ]);
        }

        shell_exec("rm -rf tests/Data/Libraries/manga5/src/[!.gitignore]*");
        shell_exec("rm -rf tests/Data/Libraries/manga5/dest/[!.gitignore]*");
    }
}
