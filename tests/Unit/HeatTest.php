<?php

namespace Tests\Unit;

use App\Archive;
use App\Heat;
use App\HeatData;
use App\Library;
use App\Manga;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @covers \App\Heat
 * @covers \App\Jobs\DecreaseHeats
 */
class HeatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param float $temperature The initial temperature.
     * @param float $rate The rate at which to increase.
     *
     * @testWith [100.0, 3.0]
     */
    public function testIncrease(float $temperature, float $rate)
    {
        $expected = $temperature + $rate;
        $actual = Heat::increase($temperature);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param float $temperature The initial temperature.
     *
     * @testWith [100.0]
     */
    public function testCooldown(float $temperature)
    {
        $after1Hour = Heat::decrease($temperature, Carbon::now()->subHour());
        $after2Hour = Heat::decrease($after1Hour, Carbon::now()->subHour());
        $after3Hour = Heat::decrease($after2Hour, Carbon::now()->subHour());

        $this->assertLessThan($temperature, $after1Hour);
        $this->assertLessThan($after1Hour, $after2Hour);
        $this->assertLessThan($after2Hour, $after3Hour);
    }

    public function testHeatConstructorThrowsOnUnsupportedModel()
    {
        $user = factory(User::class)->create();

        $this->expectException(\InvalidArgumentException::class);

        $heat = new Heat($user);
    }

    /**
     * @param float $expectedTemp The initial and expected temperature.
     *
     * @testWith [100.0]
     */
    public function testUpdate(float $expectedTemp)
    {
        $archive = factory(Archive::class)->create([
            'manga_id' => factory(Manga::class)->create([
                'library_id' => factory(Library::class)->create()
            ])
        ]);

        $heat = new Heat($archive);

        $actualTemp = $heat->temperature();

        $this->assertEquals($expectedTemp, $actualTemp);

        $heat->update();
        $actualTemp = $heat->temperature();

        $this->assertGreaterThan($expectedTemp, $actualTemp);

        $heatData = $heat->data();
        $heatData->lastUpdated = $heatData->lastUpdated->subHours(3);
        $heat->update(false);

        $priorTemp = $actualTemp;
        $actualTemp = $heat->temperature();

        $this->assertLessThan($priorTemp, $actualTemp);
    }

    /**
     * Asserts that the AdjustsHeatsJob works as expected.
     *
     * @param float $temperature The initial and expected temperature.
     *
     * @testWith [100.0]
     */
    public function testAdjustHeatsJob(float $temperature)
    {
        $archive = factory(Archive::class)->create([
            'manga_id' => factory(Manga::class)->create([
                'library_id' => factory(Library::class)->create()
            ])
        ]);

        $archiveHeat = new Heat($archive);
        $mangaHeat = new Heat($archive->manga);

        $archiveHeatData = $archiveHeat->data();
        $mangaHeatData = $mangaHeat->data();

        $archiveHeatData->lastUpdated = $archiveHeatData->lastUpdated->subHours(3);
        $mangaHeatData->lastUpdated = $mangaHeatData->lastUpdated->subHours(3);

        $archiveHeat->saveData();
        $mangaHeat->saveData();

        \Queue::push(new \App\Jobs\DecreaseHeats());

        $this->assertLessThan($temperature, $archiveHeat->temperature());
        $this->assertLessThan($temperature, $mangaHeat->temperature());
    }
}
