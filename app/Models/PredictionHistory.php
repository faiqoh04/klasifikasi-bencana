<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PredictionHistory extends Model
{
    protected $table = 'prediction_histories';

    protected $fillable = [

        'historical_disaster_id',

        'prediction_date',

        'algorithm',

        'predicted_level',

        'probability_low',

        'probability_medium',

        'probability_high'

    ];

    protected $casts = [

        'prediction_date' => 'datetime',

        'probability_low' => 'decimal:2',

        'probability_medium' => 'decimal:2',

        'probability_high' => 'decimal:2'

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    public function historicalDisaster(): BelongsTo
    {
        return $this->belongsTo(
            HistoricalDisaster::class,
            'historical_disaster_id'
        );
    }

}