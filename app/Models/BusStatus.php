<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusStatus extends Model
{
    use HasFactory;

    protected $fillable = ['bus_id', 'status', 'reason'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
