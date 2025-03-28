<?php

namespace App\Filament\Resources\BusStopResource\Pages;

use App\Filament\Resources\BusStopResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusStop extends EditRecord
{
    protected static string $resource = BusStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
