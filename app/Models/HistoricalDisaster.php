<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoricalDisaster extends Model
{
    protected $table = 'historical_disasters';

    protected $fillable = [

        'bpbd_log_id',
        'disaster_type',
        'event_date',
        'regency',
        'area',
        'latitude',
        'longitude',
        'weather',
        'chronology',
        'dead',
        'missing',
        'serious_wound',
        'minor_injuries',
        'damage',
        'damage_clean',
        'losses',
        'response',
        'photos',
        'source',
        'status',
        'level_bpbd'

    ];

    protected $casts = [

        'event_date' => 'date',

        'latitude' => 'decimal:7',

        'longitude' => 'decimal:7',

        'losses' => 'decimal:2'

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function predictionHistories(): HasMany
    {
        return $this->hasMany(
            PredictionHistory::class,
            'historical_disaster_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeYear(Builder $query, $year)
    {
        return $query->whereYear('event_date', $year);
    }

    public function scopeRegency(Builder $query, $regency)
    {
        return $query->where('regency', $regency);
    }

    public function scopeDisaster(Builder $query, $type)
    {
        return $query->where('disaster_type', $type);
    }

    public function scopeLevel(Builder $query, $level)
    {
        return $query->where('level_bpbd', $level);
    }

}