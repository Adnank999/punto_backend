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
        Schema::create('bus_stop_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('bus_stop_id')->constrained()->onDelete('cascade'); 
            $table->bigInteger('expected_arrival_time');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');
            $table->integer('route_stop_order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_stop_schedules');
    }
};
