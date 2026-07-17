<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prediction_histories', function (Blueprint $table) {

    $table->id();

    $table->foreignId('historical_disaster_id')
            ->nullable()
            ->constrained('historical_disasters')
            ->nullOnDelete();

    $table->dateTime('prediction_date');

    $table->string('algorithm');

    $table->enum('predicted_level',[
        'RENDAH',
        'SEDANG',
        'TINGGI'
    ]);

    $table->decimal('probability_low',5,2);

    $table->decimal('probability_medium',5,2);

    $table->decimal('probability_high',5,2);

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prediction_histories');
    }
};
