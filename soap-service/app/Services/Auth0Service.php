<?php

namespace App\Services;

use App\Dto\CreateClientInput;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Entities\Client as ClientEntity;

class Auth0Service
{
    private EntityManagerInterface $em;


    private $auth0Config;
    private $guzzleClient;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->guzzleClient = new Client();
        $this->auth0Config = [
            'domain' => config('services.auth0.domain'),
            'client_id' => config('services.auth0.api_client_id'),
            'client_secret' => config('services.auth0.api_client_secret'),
            'audience' => config('services.auth0.audience'),
        ];
    }

    private function request(string $method, string $url, array $data = [], array $headers = []): array
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders($headers)
            ->$method($this->auth0Config['domain'] . $url, $data);
        if ($response->successful()) {
            return $response->json();
        }
        throw new \Exception($response->body());
    }

    private function getApiToken(): string
    {
        $token = Cache::get('access-token');
        if ($token) {
            return $token;
        }
        $form = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->auth0Config['client_id'],
            'client_secret' => $this->auth0Config['client_secret'],
            'audience' => $this->auth0Config['audience'],
        ];
        $body = $this->request('post', '/oauth/token', $form, [
            'Content-Type' => 'application/json',
        ]);

        $token = $body['access_token'] ?? null;

        if ($token) {
            Cache::put('access-token', $token, 24 * 60 * 60); // Cache for 24 hours
        }

        return $token;
    }

    /**
     * @throws \Exception
     */
    public function createUser(CreateClientInput $user): array
    {
        $accessToken = $this->getApiToken();
        $form = [
            'email' => $user->getEmail(),
            'given_name' => $user->getNames(),
            'family_name' => $user->getNames(),
            'name' => $user->getNames(),
            'password' => $user->getPassword(),
            'connection' => 'Username-Password-Authentication',
        ];

        return $this->request('post', '/api/v2/users', $form, [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer $accessToken",
        ]);
    }

    public function validateClientFromAuthPayload(object $payload): ClientEntity
    {
        $payload = json_decode(json_encode($payload), true);
        $auth0Id = explode('|', $payload['sub'])[1];
        $client =  $this->em->getRepository(ClientEntity::class)->findOneBy([
            'auth0Id' => $auth0Id,
        ]);
        if(!$client){
            throw new \Exception('Client not found');
        }
        return $client;
    }
}
