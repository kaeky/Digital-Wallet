<?php

namespace App\Http\Middleware;

use App\Dto\ResponseDto;
use App\Services\Auth0Service;
use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;

class CheckAuth0Token
{
    private $jwksUri;
    private $audience;
    private $issuer;

    private $auth0Service;
    public function __construct(Auth0Service $auth0Service)
    {
        $this->auth0Service = $auth0Service;
        $this->jwksUri = config('services.auth0.domain') . '/.well-known/jwks.json';
        $this->audience = config('services.auth0.audience');
        $this->issuer = config('services.auth0.domain') . '/';
    }

    public function handle(Request $request, Closure $next)
    {
        $content = $request->getContent();
        $dom = new \DOMDocument();
        $dom->loadXML($content);
        $xpath = new \DOMXPath($dom);
        $createClientNode = $xpath->query('//ser:createClient')->item(0);
        if (!empty($createClientNode) && $createClientNode->nodeName === 'ser:createClient') {
            return $next($request);
        }

        $token = $this->getTokenFromRequest($request);
        if (!$token) {
            return response()->json(new ResponseDto(false, 401, 'No autorizado', []), 401);
        }
        try {
            $decodedToken = $this->validateToken($token);
            $user = $this->auth0Service->validateClientFromAuthPayload($decodedToken);
            $user = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getNames(),
                'document' => $user->getDocument(),
                'cellphone' => $user->getCellphone(),
            ];
            $request->merge(['user' => $user]);
        } catch (\Exception) {
            return response()->json(new ResponseDto(false, 401, 'No autorizado', []), 401);
        }

        return $next($request);
    }
    /**
     * Extract token from header or cookies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    private function getTokenFromRequest(Request $request)
    {
        // Try to get the token from Authorization header
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // If not in the header, try to get it from cookies
        $cookies = $request->cookies->all();
        return $cookies['access_token'] ?? null;
    }

    /**
     * Validate and decode the JWT.
     *
     * @param string $token
     * @return object
     * @throws \Exception
     */
    private function validateToken(string $token)
    {
        $jwks = $this->getJwks();
        $decodedHeader = JWT::jsonDecode(JWT::urlsafeB64Decode(explode('.', $token)[0]));
        if (!isset($decodedHeader->kid)) {
            throw new \Exception('Invalid token header: missing "kid".');
        }

        $key = $this->getSigningKey($jwks, $decodedHeader->kid);
        return JWT::decode($token, new Key($key, 'RS256'));
    }

    /**
     * Fetch JWKS from Auth0.
     *
     * @return array
     * @throws \Exception
     */
    private function getJwks()
    {
        $response = Http::withOptions(['verify' => false])->get($this->jwksUri);
        if (!$response->successful()) {
            throw new \Exception('Unable to fetch JWKS.');
        }
        return $response->json();
    }

    /**
     * Get the signing key from JWKS.
     *
     * @param array $jwks
     * @param string $kid
     * @return string
     * @throws \Exception
     */
    private function getSigningKey(array $jwks, string $kid)
    {
        foreach ($jwks['keys'] as $key) {
            if ($key['kid'] === $kid) {
                return "-----BEGIN CERTIFICATE-----\n" .
                    chunk_split($key['x5c'][0], 64, "\n") .
                    "-----END CERTIFICATE-----\n";
            }
        }

        throw new \Exception('No se encontró una clave coincidente para "kid".');
    }
}
