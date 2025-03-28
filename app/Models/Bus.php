<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = ['gps_device_id', 'route_id', 'current_latitude', 'current_longitude', 'active'];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }


    public function statuses()
    {
        return $this->hasMany(BusStatus::class);
    }
}
