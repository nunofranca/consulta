<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
//        $totalCadastro = Cache::remember('totalParticipant', '60000', function (){
//           return Participant::get();
//        });

        $totalCadastro = Participant::query();


        $totalRange = $totalCadastro->whereBetween('dateBirth', [Carbon::make('1970-01-01'), Carbon::make('1989-01-01')]);

        $totalNotKey = $totalCadastro
            ->wherePix(false)
            ->whereBetween('dateBirth', [Carbon::make('1970-01-01'), Carbon::make('1989-01-01')]);

        $waitingTest = $totalCadastro
            ->whereNull('pix')
            ->whereBetween('dateBirth', [Carbon::make('1970-01-01'), Carbon::make('1989-01-01')]);


        return [
            Stat::make('Total de cadasto', count($totalCadastro->get())),
            Stat::make('Total entre 1970 & 1983', count($totalRange->get())),
            Stat::make('1970 & 1983 - Sem chave', count($totalNotKey->get())),
            Stat::make('1970 & 1983 - Aguardando Teste', count( $waitingTest->get())),
            Stat::make('Average time on page', '3:12'),
        ];
    }
}
