<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStopSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'bus_stop_id', 'expected_arrival_time','route_id','route_stop_order'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function busStop()
    {
        return $this->belongsTo(BusStop::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
