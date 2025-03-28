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
        // Schema::create('route_stops', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('bus_stop_id')->constrained('bus_stops')->onDelete('cascade');
        //     $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
        //     $table->integer('predefined_time'); 
        //     $table->integer('predefined_direction'); 
        //     $table->integer('route_order');
        //     $table->boolean('recent_bus_stop_match')->default(false);
        //     $table->timestamps();
        // });

        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_stop_id')->constrained('bus_stops')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->integer('route_order')->default(0);
            $table->boolean('recent_bus_stop_match')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_stops');
    }
};
