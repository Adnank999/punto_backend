<?php

namespace App\Http\Controllers;

use App\Http\Resources\BusStatusResource;
use App\Models\BusStatus;
use Illuminate\Http\Request;

class BusStatusController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'status' => 'required|boolean',
            'reason' => 'nullable|string',
        ]);

        $busStatus = BusStatus::updateOrCreate(
            ['bus_id' => $request->bus_id],
            $request->only(['status', 'reason'])
        );

        return new BusStatusResource($busStatus);
    }
}
