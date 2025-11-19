<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parking_rates', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_type', ['motor','mobil'])->unique();
            $table->unsignedInteger('hourly_rate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_rates');
    }
};
