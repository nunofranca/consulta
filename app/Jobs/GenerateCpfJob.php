<?php

namespace App\Jobs;

use App\Models\Participant;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Predis\Command\Argument\Server\To;

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
            $token = Cache::remember('tokens', 600, function () {
                return Token::get();
            });

            if ($token->isEmpty()) return;


//            $sort = rand(1, 5);
//            switch ($sort) {
//                case 1:
//                    $token= "135825705QuemMTlxHt245229192";
//                    break;
//                case 2:
//                    $token= "135829910gHuDBbYkhR245236784";
//                    break;
//                case 3:
//                    $token= "135834115fwqPZINCXx245244376";
//                    break;
//                case 4:
//                    $token= "135842525GaHRxlBdhE245259560";
//                    break;
//                case 5:
//                    $token= "135846730eMtUNchDpu245267152";
//                    break;
//            }


            $token = $token->random();
            $cpfValidated = Http::get('https://ws.hubdodesenvolvedor.com.br/v2/nome_cpf/?cpf=' . $cpf . '&last_update=2&token=' . $token);

            if ($cpfValidated['return'] == 'NOK') $token->delete();

            if (!$cpfValidated->json()['status'] || !isset($cpfValidated->json()['result'])) return;


            $carbonDataVerificar = Carbon::make(Str::replace('/', '-', $cpfValidated['result']['data_de_nascimento']));


            if ($carbonDataVerificar->between(Carbon::make('1950-01-01'), Carbon::make('2003-12-31'))) {

                Participant::query()->firstOrCreate(
                    [
                        'cpf' => $cpf
                    ],
                    [
                        'name' => $cpfValidated['result']['nome'],
                        'dateBirth' => $carbonDataVerificar,
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
