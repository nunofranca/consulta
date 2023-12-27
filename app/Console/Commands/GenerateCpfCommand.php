<?php

namespace App\Console\Commands;

use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;


class GenerateCpfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generatecpf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $cpf = rand(00000000000, 99999999999);
//        $cpf = '11637563779';

        if ($this->validaCPF($cpf)) {

            $cpfValidated = Http::get('https://ws.hubdodesenvolvedor.com.br/v2/nome_cpf/?cpf=' . $cpf . '&token=135686940YSmHLpbjTv244978656');

            if (!$cpfValidated->json()['status'] || !isset($cpfValidated->json()['result'])) return;

            $carbonDataVerificar = Carbon::createFromFormat('d-m-Y', Str::replace('/', '-', $cpfValidated['result']['data_de_nascimento']));


            if ($carbonDataVerificar->between(Carbon::parse('1950-01-01'), Carbon::parse('2003-12-31'))) {

                Participant::create([
                    'name' => $cpfValidated['result']['nome'] ?: $cpfValidated['message'],
                    'dateBirth' => $cpfValidated['result']['data_de_nascimento'] ?: null,
                    'cpf' => $cpf,
                ]);
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
