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
        Schema::table('historical_disasters', function (Blueprint $table) {
            // Ubah source dan status dari VARCHAR(255) ke TEXT agar menampung data lebih panjang
            $table->text('source')->nullable()->change();
            $table->text('status')->nullable()->change();
            $table->text('weather')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historical_disasters', function (Blueprint $table) {
            $table->string('source')->nullable()->change();
            $table->string('status')->nullable()->change();
            $table->string('weather')->nullable()->change();
        });
    }
};
