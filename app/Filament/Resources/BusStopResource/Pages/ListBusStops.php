<?php

namespace App\Filament\Resources\BusStopResource\Pages;

use App\Filament\Resources\BusStopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusStops extends ListRecords
{
    protected static string $resource = BusStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
