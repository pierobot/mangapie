<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

final class HeatData
{
    /**
     * @var int
     */
    public $modelId;

    /**
     * @var float
     */
    public $temperature;

    /**
     * @var Carbon
     */
    public $lastUpdated;

    /**
     * HeatData constructor.
     *
     * @param int $modelId
     * @param float $temperature
     */
    public function __construct(int $modelId, float $temperature)
    {
        $this->modelId = $modelId;
        $this->temperature = $temperature;
        $this->lastUpdated = Carbon::now();
    }

    /**
     * Gets the key of the HeatData.
     *
     * @return string
     */
    public function key()
    {
        return self::keyFor($this->modelId);
    }

    /**
     * Generates a key for use with the cache.
     *
     * @param int $modelId
     * @return string
     */
    public static function keyFor(int $modelId)
    {
        return $modelId;
    }
}

// https://www.evanmiller.org/rank-hotness-with-newtons-law-of-cooling.html
final class Heat
{
    /**
     * @var float
     */
    protected static $defaultTemperature;
    /**
     * @var float
     */
    protected static $heatRate;
    /**
     * @var float
     */
    protected static $cooldownRate;

    /**
     * Initializes the static variables used with the heat and cooldown methods.
     *
     * @return void
     */
    private static function initialize()
    {
        if (empty(self::$defaultTemperature) || empty(self::$heatRate) || empty(self::$cooldownRate)) {
            self::$defaultTemperature = \Config::get('app.heat.default');
            self::$heatRate = \Config::get('app.heat.heat');
            self::$cooldownRate = \Config::get('app.heat.cooldown');
        }
    }

    /**
     * Gets the heat value for a given model.
     * The supported models are Archive and Manga.
     *
     * @param Model $model
     * @return float|false
     */
    public static function get(Model $model)
    {
        self::initialize();

        $data = self::data($model);

        return ! empty($data) ? $data->temperature : false;
    }

    /**
     * Updates the heat value for a given model.
     * The supported models are Archive and Manga.
     *
     * @param Model $model
     * @param bool $increment
     * @return void
     */
    public static function update(Model $model, bool $increment = true)
    {
        self::initialize();

        $heatData = self::data($model);
        if (empty($heatData)) {
            $heatData = new HeatData($model->id, self::$defaultTemperature);
            $temperature = $heatData->temperature;
        } else {
            $temperature = $increment ?
                self::heat($heatData->temperature) :
                self::cooldown($heatData->temperature, $heatData->lastUpdated);
        }

        if ($model instanceof Archive)
            self::setArchiveHeatData($model->id, $temperature);
        elseif ($model instanceof Manga)
             self::setMangaHeatData($model->id, $temperature);
    }

    /**
     * Gets the heat data for a given model.
     * The supported models are Archive and Manga.
     *
     * @param Model $model
     * @return HeatData|false
     */
    private static function data(Model $model)
    {
        if ($model instanceof Archive)
            return self::archiveHeatData($model->id);
        elseif ($model instanceof Manga)
            return self::mangaHeatData($model->id);

        return false;
    }

    /**
     * Retrieves the last heat data for a manga from the cache.
     *
     * @param int $id
     * @return HeatData
     */
    private static function mangaHeatData(int $id)
    {
        $key = HeatData::keyFor($id);

        return \Cache::tags('manga_heat')->get($key);
    }

    /**
     * Retrieves the last heat data for an archive from the cache.
     *
     * @param int $id
     * @return HeatData
     */
    private static function archiveHeatData(int $id)
    {
        $key = HeatData::keyFor($id);

        return \Cache::tags('archive_heat')->get($key);
    }

    /**
     * Sets the heat data for a manga.
     *
     * @param int $id
     * @param float $temperature
     * @return void
     */
    private static function setMangaHeatData(int $id, float $temperature)
    {
        $heatData = new HeatData($id, $temperature);

        \Cache::tags('manga_heat')->forever($heatData->key(), $heatData);
    }

    /**
     * Sets the heat data for an archive.
     *
     * @param int $id
     * @param float $temperature
     * @return void
     */
    private static function setArchiveHeatData(int $id, float $temperature)
    {
        $heatData = new HeatData($id, $temperature);

        \Cache::tags('archive_heat')->forever($heatData->key(), $heatData);
    }

    /**
     * Increases the heat value.
     *
     * @param float $lastTemperature
     * @return float
     */
    public static function heat(float $lastTemperature)
    {
        self::initialize();

        return $lastTemperature + self::$heatRate;
    }

    /**
     * Decreases the heat value.
     *
     * @param float $lastTemperature
     * @param Carbon $lastUpdated
     * @return float
     */
    public static function cooldown(float $lastTemperature, Carbon $lastUpdated)
    {
        self::initialize();

        $hourDifference = Carbon::now()->diffInHours($lastUpdated);

        return $lastTemperature * exp(-(self::$cooldownRate) * $hourDifference);
    }
}