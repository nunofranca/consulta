<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('date', function (){
    $participants = \App\Models\Participant::get();

   collect($participants)->map(function ($participant){
      $participant->update([
         'dateBirth' =>  Carbon::make(Str::replace('/', '-', $participant->dateBirth))->format('Y-m-d')

       ]);
   });
});
