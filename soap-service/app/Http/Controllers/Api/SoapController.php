<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\VirtualWalletService;
use Illuminate\Http\Request;
use Laminas\Soap\AutoDiscover;
use Laminas\Soap\Server;
use Laminas\Soap\Wsdl;

class SoapController extends Controller
{
    public function handle(Request $request)
    {
        $server = new Server(null, [
            'uri' => env('SOAP_ENDPOINT', 'http://localhost:8000/soap')
        ]);
        $server->setClass(VirtualWalletService::class);
        $server->handle();
    }

    public function wsdl()
    {
        $autoDiscover = new AutoDiscover();
        $autoDiscover->setClass(VirtualWalletService::class)
            ->setUri(env('SOAP_ENDPOINT', 'http://localhost:8000/soap'))
            ->setServiceName('VirtualWalletService');

        header('Content-Type: application/wsdl+xml');
        echo $autoDiscover->generate()->toXml();
    }
}
