<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusStopResource\Pages;
use App\Filament\Resources\BusStopResource\RelationManagers;
use App\Models\BusStop;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusStopResource extends Resource
{
    protected static ?string $model = BusStop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Bus Stop Name')
                    ->maxLength(255),

                    TextInput::make('latitude')
                    ->required()
                    ->label('Latitude')
                    ->numeric(),
    
                TextInput::make('longitude')
                    ->required()
                    ->label('Longitude')
                    ->numeric(),  
                    
                Forms\Components\TextInput::make('predefined_time')
                    ->label('Predefined Time (seconds)')
                    ->numeric()
                    ->minValue(1)
                    ->required(),

                Forms\Components\TextInput::make('predefined_radius')
                    ->label('Predefined Direction (meters)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(1000)
                    ->required(),

                Forms\Components\TextInput::make('predefined_direction')
                    ->label('Predefined Direction (degrees)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(360)
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('latitude')->sortable(),
                Tables\Columns\TextColumn::make('longitude')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\Action::make('view')
                //     ->modal('Bus Stop Details')
                //     ->modalContent(fn (BusStop $record) => view('filament.bus_stop_view', ['record' => $record])),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete')
                    ->action(fn(Collection $records) => $records->each->delete())
                    ->requiresConfirmation(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusStops::route('/'),
            // 'create' => Pages\CreateBusStop::route('/create'),
            'edit' => Pages\EditBusStop::route('/{record}/edit'),
        ];
    }
}
