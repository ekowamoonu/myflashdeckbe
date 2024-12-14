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
        Schema::create('fuel_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("vehicle_profile_id")->nullable()->default(null);
            $table->decimal('amount', 8, 2);
            $table->decimal('litres', 8, 2);
            $table->timestamp('purchase_date_and_time');
            $table->timestamps();
            $table->foreign('vehicle_profile_id')->references('id')->on('vehicle_profiles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_trackings');
    }
};
