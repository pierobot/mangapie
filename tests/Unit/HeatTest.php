<?php

namespace Tests\Unit;

use App\Archive;
use App\Heat;
use App\Library;
use App\Manga;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\Heat
 */
class HeatTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Asserts that the heat is increased as expected.
     * @param $heat
     *
     * @testWith [100.0]
     */
    public function testHeat($heat)
    {
        $expected = $heat + \Config::get('app.heat.heat');
        $actual = Heat::heat($heat);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Asserts that the heat is cooled as expected.
     *
     * @param $heat
     *
     * @testWith [100.0]
     */
    public function testCooldown($heat)
    {
        $after1Hour = Heat::cooldown($heat, Carbon::now()->subHour());
        $after2Hour = Heat::cooldown($after1Hour, Carbon::now()->subHour());
        $after3Hour = Heat::cooldown($after2Hour, Carbon::now()->subHour());

        $this->assertLessThan($heat, $after1Hour);
        $this->assertLessThan($after1Hour, $after2Hour);
        $this->assertLessThan($after2Hour, $after3Hour);
    }

    /**
     * Asserts that the get function returns false on an unsupported model.
     */
    public function testGetOnUnsupportedModelReturnsFalse()
    {
        $manga = factory(User::class)->make();

        $heat = Heat::get($manga);

        $this->assertFalse($heat);
    }

    /**
     * Asserts that the update function for an archive works as intended.
     */
    public function testUpdate()
    {
        $archive = factory(Archive::class)->create([
            'manga_id' => factory(Manga::class)->create([
                'library_id' => factory(Library::class)->create()
            ])
        ]);

        Heat::update($archive);

        $expectedHeat = \Config::get('app.heat.default');
        $actualHeat = Heat::get($archive);

        $this->assertEquals($expectedHeat, $actualHeat);

        Heat::update($archive);

        $actualHeat = Heat::get($archive);

        $this->assertGreaterThan($expectedHeat, $actualHeat);
    }
}
