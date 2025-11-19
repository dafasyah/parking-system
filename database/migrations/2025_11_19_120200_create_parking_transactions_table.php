<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code')->unique();
            $table->string('license_plate');
            $table->enum('vehicle_type', ['motor','mobil']);
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->unsignedInteger('duration_hours')->nullable();
            $table->unsignedInteger('parking_fee')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->enum('status',['parked','completed'])->default('parked');
            $table->foreignId('processed_by_entry')->constrained('users');
            $table->foreignId('processed_by_exit')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index('license_plate');
            $table->index('status');
            $table->index('entry_time');
            $table->index('exit_time');
            $table->index('vehicle_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_transactions');
    }
};
