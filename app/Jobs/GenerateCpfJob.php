<?php

namespace App\Jobs;

use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GenerateCpfJob implements ShouldQueue
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

        $cpf = rand(00000000000, 99999999999);

        if ($this->validaCPF($cpf)) {

            $cpfValidated = Http::get('https://ws.hubdodesenvolvedor.com.br/v2/nome_cpf/?cpf=' . $cpf . '&token=135703760amJWMwVtSX245009024');

            if (!$cpfValidated->json()['status'] || !isset($cpfValidated->json()['result'])) return;

            $carbonDataVerificar = Carbon::createFromFormat('d-m-Y', Str::replace('/', '-', $cpfValidated['result']['data_de_nascimento']));


            if ($carbonDataVerificar->between(Carbon::parse('1950-01-01'), Carbon::parse('2003-12-31'))) {

                Participant::query()->firstOrCreate(
                    [
                        'cpf' => $cpf
                    ],
                    [
                        'name' => $cpfValidated['result']['nome'] ?: $cpfValidated['message'],
                        'dateBirth' => $cpfValidated['result']['data_de_nascimento'] ?: null,
                    ]

                );
            }
        }
    }

    private function validaCPF($cpf)
    {

        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;

    }
}
