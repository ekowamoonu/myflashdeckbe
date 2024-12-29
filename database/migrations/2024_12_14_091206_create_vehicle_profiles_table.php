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
        Schema::create('vehicle_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->string('name', 35);
            $table->string('make', 20)->nullable()->default(null);
            $table->string('model_year', 4)->nullable()->default(null);
            $table->string('license_plate', 20)->nullable()->default(null);
            $table->dateTime('last_insurance_renewal')->nullable()->default(null);
            $table->json('photos_of_vehicle')->nullable()->default(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_profiles');
    }
};
