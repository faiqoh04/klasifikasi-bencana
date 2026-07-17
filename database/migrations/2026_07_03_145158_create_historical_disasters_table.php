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
        Schema::create('historical_disasters', function (Blueprint $table) {

    $table->id();

    $table->unsignedBigInteger('bpbd_log_id')->nullable();

    $table->string('disaster_type',100);

    $table->date('event_date');

    $table->string('regency',100);

    $table->text('area')->nullable();

    $table->decimal('latitude',10,7)->nullable();

    $table->decimal('longitude',10,7)->nullable();

    $table->string('weather')->nullable();

    $table->longText('chronology')->nullable();

    $table->integer('dead')->default(0);

    $table->integer('missing')->default(0);

    $table->integer('serious_wound')->default(0);

    $table->integer('minor_injuries')->default(0);

    $table->longText('damage')->nullable();

    $table->longText('damage_clean')->nullable();

    $table->decimal('losses',20,2)->nullable();

    $table->longText('response')->nullable();

    $table->longText('photos')->nullable();

    $table->string('source')->nullable();

    $table->string('status')->nullable();

    $table->enum('level_bpbd',['RENDAH','SEDANG','TINGGI']);

    $table->timestamps();

    $table->index('event_date');

    $table->index('regency');

    $table->index('disaster_type');

    $table->index('level_bpbd');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_disasters');
    }
};
