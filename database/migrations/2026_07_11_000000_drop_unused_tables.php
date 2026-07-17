<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('model_metrics');
        Schema::dropIfExists('import_histories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
