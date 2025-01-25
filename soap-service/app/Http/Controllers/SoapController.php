<?php

namespace App\Http\Controllers;

use App\Services\WalletSoapService;
use Illuminate\Http\Request;
use Laminas\Soap\AutoDiscover;
use Laminas\Soap\Server;
use App\Services\Auth0Service;
use Doctrine\ORM\EntityManagerInterface;

class SoapController extends Controller
{

    private EntityManagerInterface $em;
    private Auth0Service $auth0Service;

    public function __construct(EntityManagerInterface $em, Auth0Service $auth0Service)
    {
        $this->em = $em;
        $this->auth0Service = $auth0Service;
    }

    /**
     * Generate WSDL dynamically.
     */
    public function wsdl()
    {
        $autoDiscover = new AutoDiscover();
        $autoDiscover->setClass(WalletSoapService::class)
            ->setUri(env('SOAP_ENDPOINT', 'http://localhost:8000/soap'))
            ->setServiceName('WalletSoapService');

        header('Content-Type: application/wsdl+xml');
        echo $autoDiscover->generate()->toXml();
    }

    /**
     * Handle SOAP requests (HTTP POST).
     */
    public function handle(Request $request)
    {
        $service = new WalletSoapService($this->em, $this->auth0Service);

        $server = new Server(null, [
            'uri' => env('SOAP_ENDPOINT', 'http://localhost:8000/soap')
        ]);
        $server->setObject($service);

        ob_start();
        $server->handle();
        $response = ob_get_contents();
        ob_end_clean();

        return response($response, 200)
            ->header('Content-Type', 'text/xml; charset=utf-8');
    }
}
