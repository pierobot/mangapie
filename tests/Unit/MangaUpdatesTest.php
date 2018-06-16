<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Artist;
use App\AssociatedName;
use App\Author;
use App\Genre;
use App\Library;
use App\Manga;
use App\MangaUpdates;

/**
 * @covers \App\MangaUpdates
 */
class MangaUpdatesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed();

        $this->app->bind(\App\Observers\MangaObserver::class, function () {
            return $this->getMockBuilder(\App\Observers\MangaObserver::class)->disableOriginalConstructor()->getMock();
        });
    }

    /**
     * @testWith [118, "Yu Yu Hakusho", "Manga", "", ["Отчёт о буйстве духов", "幽游白书", "幽遊白書", "คนเก่งฟ้าประทาน", "Hành Trình U Linh Giới", "Yū Yū Hakush", "Yuu Yuu Hakusho", "YuYu Hakusho"], ["TOGASHI Yoshihiro"], ["TOGASHI Yoshihiro"], ["Action","Adventure","Comedy","Drama","Fantasy","Shounen","Supernatural"], 1990]
     *
     * Yes, I know "Yū Yū Hakush" is missing the o.
     * If this ever fails in the future, it's probably because they finally fixed it.
     */
    public function testAutofillLatin($id, $name, $type, $description, $assocNames, $authors, $artists, $genres, $year)
    {
        $manga = factory(Manga::class)->create([
            'name' => $name,
            'library_id' => factory(Library::class)->create()->getId()
        ]);

        $this->assertTrue(MangaUpdates::autofill($manga));

        $this->assertEquals($id, $manga->getMangaUpdatesId());
//        $this->assertEquals($description, $manga->getDescription());
        $this->assertEquals($type, $manga->getType());
        $this->assertEquals($year, $manga->getYear());

        $actualAssocNames = array_map(function (AssociatedName $assocName) {
            return $assocName->getName();
        }, $manga->getAssociatedNames());

        $actualArtists = array_map(function (Artist $artist) {
            return $artist->getName();
        }, $manga->getArtists());

        $actualAuthors = array_map(function (Author $author) {
            return $author->getName();
        }, $manga->getAuthors());

        $actualGenres = array_map(function (Genre $genre) {
            return $genre->getName();
        }, $manga->getGenres());

        $this->assertEquals($assocNames, $actualAssocNames);
        $this->assertEquals($artists, $actualArtists);
        $this->assertEquals($authors, $actualAuthors);
        $this->assertEquals($genres, $actualGenres);
    }

    /**
     * @testWith [1051, "めぞん一刻", "Manga", "Travel into Japan's nuttiest apartment house and meet its volatile inhabitants: Kyoko, the beautiful and mysterious new apartment manager; Yusaku, the exam-addled college student; Mrs. Ichinose, the drunken gossip; Kentaro, her bratty son; Akemi, the boozy bar hostess; and the mooching and peeping Mr. Yotsuya.", ["Доходный дом Иккоку", "めぞん一刻", "相聚一刻", "Mezon Ikkoku"], ["TAKAHASHI Rumiko"], ["TAKAHASHI Rumiko"], ["Comedy", "Drama", "Romance", "Seinen", "Slice of Life"], 1980]
     */
    public function testAutofillJapanese($id, $name, $type, $description, $assocNames, $authors, $artists, $genres, $year)
    {
        $manga = factory(Manga::class)->create([
            'name' => $name,
            'library_id' => factory(Library::class)->create()->getId()
        ]);

        $this->assertTrue(MangaUpdates::autofill($manga));

        $this->assertEquals($id, $manga->getMangaUpdatesId());
        $this->assertEquals($description, $manga->getDescription());
        $this->assertEquals($type, $manga->getType());
        $this->assertEquals($year, $manga->getYear());

        $actualAssocNames = array_map(function (AssociatedName $assocName) {
            return $assocName->getName();
        }, $manga->getAssociatedNames());

        $actualArtists = array_map(function (Artist $artist) {
            return $artist->getName();
        }, $manga->getArtists());

        $actualAuthors = array_map(function (Author $author) {
            return $author->getName();
        }, $manga->getAuthors());

        $actualGenres = array_map(function (Genre $genre) {
            return $genre->getName();
        }, $manga->getGenres());

        $this->assertEquals($assocNames, $actualAssocNames);
        $this->assertEquals($artists, $actualArtists);
        $this->assertEquals($authors, $actualAuthors);
        $this->assertEquals($genres, $actualGenres);
    }
}
