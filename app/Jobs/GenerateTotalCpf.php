<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use App\Models\Token;

class GenerateTotalCpf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tokens = Cache::remember('tokens', 600, function () {
            return Token::get();
        });
        if (count($tokens) == 0) return;
        $sort = rand(0, count($tokens) - 1);

        $token = $tokens[$sort];


        for ($i=0;$i<2000;$i++) {
           GenerateCpfJob::dispatch($token)->onQueue('genreateCpf');
        }
    }
}
