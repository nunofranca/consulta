<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Xpdeal\Pixphp\Services\PixService;

class HomeController extends Controller
{
    public function index()
    {

        $payload = (new PixService())
            ->setPixKey('alberttttojrfsa@gmail.com')
            ->setDescription('venda de sapato')
            ->setMerchantName('Alberto Franca')
            ->setMerchantCity('Feira de Santana')
            ->setTxId('429.906.705.30')
            ->setAmount(120);

        echo $payload->getPayload();

        // Get payload and QRCode (array)
        dd($payload->getPayloadAndQrcode());
    }

}
