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
        Schema::create('packing_space_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parking_space_id");
            $table->unsignedDouble("weekday_price");
            $table->unsignedDouble("weekend_price");
            $table->unsignedDouble("summer_price_multiplier");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_space_pricings');
    }
};
