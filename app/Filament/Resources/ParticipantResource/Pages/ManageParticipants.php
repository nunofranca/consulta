<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageParticipants extends ManageRecords
{
    protected static string $resource = ParticipantResource::class;

    public function getTabs(): array
    {
        return [
            'Sem chave' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->wherePix(false)->whereStatus(true)),
            'Todos' => Tab::make()

        ];
    }
}
