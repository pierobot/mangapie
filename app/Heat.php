<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->modelId = $model->id;
        $this->temperature = \Cache::tags(['config', 'heat'])->get('default', 100.0);
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

    /**
     * Sets the temperature.
     *
     * @param float $temperature
     * @return void
     */
    public function setTemperature(float $temperature)
    {
        $this->temperature = $temperature;
        $this->lastUpdated = Carbon::now();
    }

    /**
     * Gets the temperature.
     *
     * @return float
     */
    public function temperature()
    {
        return $this->temperature;
    }

    /**
     * Gets the time when a user's heat data was last updated.
     *
     * @return Carbon
     */
    public function lastUpdated()
    {
        return $this->lastUpdated;
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
     * @var Model
     */
    private $model;
    /**
     * @var HeatData
     */
    private $data;

    /**
     * Heat constructor.
     *
     * @param Model $model
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(Model $model)
    {
        self::initialize();

        if (! ($model instanceof Archive) && ! ($model instanceof Manga))
            throw new \InvalidArgumentException('Unsupported model.');

        $this->model = $model;
        $this->data = $this->data();
    }

    /**
     * Initializes the static variables used with the increase and decrease methods.
     *
     * @return void
     */
    private static function initialize()
    {
        if (empty(self::$defaultTemperature) || empty(self::$heatRate) || empty(self::$cooldownRate)) {
            self::$defaultTemperature = \Cache::tags(['config', 'heat'])->get('default', 100.0);
            self::$heatRate = \Cache::tags(['config', 'heat'])->get('heat', 3.0);
            self::$cooldownRate = \Cache::tags(['config', 'heat'])->get('cooldown', 0.01);
        }
    }

    /**
     * Gets the temperature.
     *
     * @return float|false
     */
    public function temperature()
    {
        return ! empty($this->data) ? $this->data->temperature() : false;
    }

    /**
     * Gets the time when the data was last updated.
     *
     * @return Carbon|false
     */
    public function lastUpdated()
    {
        return ! empty($this->data) ? $this->data->lastUpdated() : false;
    }

    /**
     * Updates the heat value for a given model.
     * The supported models are Archive and Manga.
     *
     * @param bool $increase
     * @return void
     */
    public function update(bool $increase = true)
    {
        self::initialize();

        if (! empty($this->data)) {
            $temperature = $increase ?
                self::increase($this->data->temperature()) :
                self::decrease($this->data->temperature(), $this->data->lastUpdated());

            $this->data->setTemperature($temperature);

            $this->saveData();
        }
    }

    /**
     * Updates the heat values for all users and given models.
     * The values will be decreased.
     *
     * @param Collection $models
     *
     * @throws \InvalidArgumentException
     */
    public static function updateAll(Collection $models)
    {
        foreach ($models as $model) {
            $heat = new Heat($model);
            $heat->update(false);
        }
    }

    /**
     * Saves the HeatData to cache.
     *
     * @return void
     */
    public function saveData()
    {
        if ($this->model instanceof Archive)
            self::setArchiveHeatData($this->data);
        elseif ($this->model instanceof Manga)
            self::setMangaHeatData($this->data);

        $this->data = $this->data();
    }

    /**
     * Gets the heat data for a given model.
     * The supported models are Archive and Manga.
     *
     * @return HeatData
     */
    public function data()
    {
        if ($this->model instanceof Archive)
            $data = self::archiveHeatData($this->model);
        elseif ($this->model instanceof Manga)
            $data = self::mangaHeatData($this->model);
        else
            return null;

        if (empty($data)) {
            $data = $this->data = new HeatData($this->model);
            $this->saveData();
        }

        return $data;
    }

    /**
     * Retrieves the last heat data for a manga from the cache.
     *
     * @param Manga $manga
     * @return HeatData
     */
    private static function mangaHeatData(Manga $manga)
    {
        $key = HeatData::keyFor($manga->id);

        return \Cache::tags(['heat', 'manga'])->get($key);
    }

    /**
     * Retrieves the last heat data for an archive from the cache.
     *
     * @param Archive $archive
     * @return HeatData
     */
    private static function archiveHeatData(Archive $archive)
    {
        $key = HeatData::keyFor($archive->id);

        return \Cache::tags(['heat', 'archive'])->get($key);
    }

    /**
     * Sets the heat data for a manga.
     *
     * @param HeatData $heatData
     * @return void
     */
    private static function setMangaHeatData(HeatData $heatData)
    {
        \Cache::tags(['heat', 'manga'])->forever($heatData->key(), $heatData);
    }

    /**
     * Sets the heat data for an archive.
     *
     * @param HeatData $heatData
     * @return void
     */
    private static function setArchiveHeatData(HeatData $heatData)
    {
        \Cache::tags(['heat', 'archive'])->forever($heatData->key(), $heatData);
    }

    /**
     * Increases the heat value.
     *
     * @param float $lastTemperature
     * @return float
     */
    public static function increase(float $lastTemperature)
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
    public static function decrease(float $lastTemperature, Carbon $lastUpdated)
    {
        self::initialize();

        $hourDifference = Carbon::now()->diffInHours($lastUpdated);

        return $lastTemperature * exp(-(self::$cooldownRate) * $hourDifference);
    }
}