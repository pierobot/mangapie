<?php

namespace Tests\Unit;

use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\AssociatedName;
use App\Genre;
use App\Library;
use App\Manga;
use App\Person;
use App\Sources\MangaUpdates;

/**
 * @covers \App\Sources\MangaUpdates
 * @covers \App\Sources\MangaUpdates\Series
 */
class MangaUpdatesTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();

        $this->seed();

        $this->app->bind(\App\Observers\MangaObserver::class, function () {
            return $this->getMockBuilder(\App\Observers\MangaObserver::class)->disableOriginalConstructor()->getMock();
        });
    }

    /**
     * @testWith [118, "Yu Yu Hakusho", "Manga", "", ["Отчёт о буйстве духов", "幽游白书", "幽遊白書", "คนเก่งฟ้าประทาน", "Hành Trình U Linh Giới", "Yū Yū Hakush", "Yuu Yuu Hakusho", "YuYu Hakusho"], ["TOGASHI Yoshihiro"], ["TOGASHI Yoshihiro"], ["Action","Adventure","Comedy","Drama","Shounen","Supernatural"], 1990]
     *
     * Yes, I know "Yū Yū Hakush" is missing the o.
     * If this ever fails in the future, it's probably because they finally fixed it.
     */
    public function testAutofillLatin($id, $name, $type, $description, $assocNames, $authors, $artists, $genres, $year)
    {
        /** @var Manga $manga */
        $manga = factory(Manga::class)->create([
            'name' => $name,
            'library_id' => factory(Library::class)->create()->getId()
        ]);

        $this->assertTrue(MangaUpdates::autofill($manga));

        $this->assertEquals($id, $manga->mu_id);
        $this->assertEquals($description, $manga->description);
        $this->assertEquals($type, $manga->type);
        $this->assertEquals($year, $manga->year);

        $actualAssocNames = $manga->associatedNames
            ->transform(function (AssociatedName $assocName) {
                return $assocName->name;
            })
            ->toArray();

        $actualArtists = $manga->artists
            ->transform(function (Person $artist) {
                return $artist->name;
            })
            ->toArray();

        $actualAuthors = $manga->authors
            ->transform(function (Person $author) {
                return $author->name;
            })
            ->toArray();

        $actualGenres = $manga->genres
            ->transform(function (Genre $genre) {
                return $genre->name;
            })
            ->toArray();

        $this->assertTrue(count(array_diff($actualAssocNames, $assocNames)) == 0);
        $this->assertTrue(count(array_diff($actualArtists, $artists)) == 0);
        $this->assertTrue(count(array_diff($actualAuthors, $authors)) == 0);
        $this->assertTrue(count(array_diff($actualGenres, $genres)) == 0);
    }

    /**
     * @testWith [1051, "めぞん一刻", "Manga", "Travel into Japan's nuttiest apartment house and meet its volatile inhabitants: Kyoko, the beautiful and mysterious new apartment manager; Yusaku, the exam-addled college student; Mrs. Ichinose, the drunken gossip; Kentaro, her bratty son; Akemi, the boozy bar hostess; and the mooching and peeping Mr. Yotsuya.", ["Mezon Ikkoku", "Nhà trọ Nhất Khắc", "Доходный дом Иккоку", "めぞん一刻", "相聚一刻", "도레미 하우스", "메종일각"], ["TAKAHASHI Rumiko"], ["TAKAHASHI Rumiko"], ["Comedy", "Drama", "Romance", "Seinen", "Slice of Life"], 1980]
     */
    public function testAutofillJapanese($id, $name, $type, $description, $assocNames, $authors, $artists, $genres, $year)
    {
        $manga = factory(Manga::class)->create([
            'name' => $name,
            'library_id' => factory(Library::class)->create()->getId()
        ]);

        $this->assertTrue(MangaUpdates::autofill($manga));

        $this->assertEquals($id, $manga->mu_id);
        $this->assertEquals($description, $manga->description);
        $this->assertEquals($type, $manga->type);
        $this->assertEquals($year, $manga->year);

        $actualAssocNames = $manga->associatedNames
            ->transform(function (AssociatedName $assocName) {
                return $assocName->name;
            })
            ->toArray();

        $actualArtists = $manga->artists
            ->transform(function (Person $artist) {
                return $artist->name;
            })
            ->toArray();

        $actualAuthors = $manga->authors
            ->transform(function (Person $author) {
                return $author->name;
            })
            ->toArray();

        $actualGenres = $manga->genres
            ->transform(function (Genre $genre) {
                return $genre->name;
            })
            ->toArray();

        $this->assertTrue(count(array_diff($actualAssocNames, $assocNames)) == 0);
        $this->assertTrue(count(array_diff($actualArtists, $artists)) == 0);
        $this->assertTrue(count(array_diff($actualAuthors, $authors)) == 0);
        $this->assertTrue(count(array_diff($actualGenres, $genres)) == 0);
    }
}
