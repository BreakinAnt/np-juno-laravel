<?php

namespace App\Juno\Services;

use App\Juno\Models\JunoEnvironment;
use App\Juno\Models\JunoPix;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use App\Juno\Models\JunoToken;
use Illuminate\Support\Str;
use ErrorException;

class JunoAPI
{
    /**
     * Criação do Authorization Token para ser usado no BearerAuth.
     * 
     * @param string $resourceToken "exemplo-client-id:exemplo-client-secret" convertido para Base64.
     * 
     * @return Eloquent Retorna objeto do banco com o token.
    */
    protected function getAuthToken(string $resourceToken)
    {
        $token = JunoToken::first();
        $createNew = true;
        $hasExpired = true;

        if($token){
            $hasExpired = $token->checkTime();
            $createNew = false;
        } 

        if($hasExpired && $createNew == false){
            $token->delete();
        }

        if($createNew){
            $token = new JunoToken();
        }

        if($hasExpired || $createNew){
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Basic '.$resourceToken,
            ];
    
            $body = "grant_type=client_credentials";
            
            if($this->environment == JunoEnvironment::SANDBOX){
                $request = new GuzzleRequest('POST', 'https://sandbox.boletobancario.com/authorization-server/oauth/token', $headers, $body);
            } else {
                $request = new GuzzleRequest('POST', 'https://api.juno.com.br/authorization-server/oauth/token', $headers, $body);
            }
    
            $client = new GuzzleClient();
            try {
                $response = $client->send($request);
            } catch(GuzzleClientException $e) {
                $response = $e->getResponse();
            }

            switch($response->getStatusCode()){
                case 400:
                    return ['generic_error' => 'Requisição inválida (Bad Request)', 'status_code' => 400];
                break;
                case 401:
                    return ['generic_error' => 'Token de autorização inválido ou expirado (Unauthorized)', 'status_code' => 401];
                break;
                case 500:
                    return ['generic_error' => 'Erro interno do servidor (Internal Server generic_error)', 'status_code' => 500];
                break;
            }
    
            $payload = json_decode($response->getBody()->getContents());

            $token->fill((array) $payload);
            $token->save();
        } 

        return $token;
    }

    protected function ApiCreateCharge($charge, $billing, $authToken)
    {
        $headers = [
            'Authorization' => 'Bearer '.$authToken,
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Resource-Token' => $this->privateToken,
            'X-Api-Version' => 2
        ];

        $body = json_encode(['charge' => $charge, 'billing' => $billing]);
        
        $request = new GuzzleRequest('POST', JunoEnvironment::getUrl($this->environment).'charges', $headers, $body);

        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
        } catch(GuzzleClientException $e) {
            $response = $e->getResponse();
        }

        $payload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() >= 400){
            throw new ErrorException(json_encode($payload));
        }

        return $payload;
    }

    protected function ApiCreatePaymentCharge($charge, $authToken)
    {
        $headers = [
            'Authorization' => 'Bearer '.$authToken,
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Resource-Token' => $this->privateToken,
            'X-Api-Version' => 2
        ];

        $body = json_encode($charge);
        
        $request = new GuzzleRequest('POST', JunoEnvironment::getUrl($this->environment).'payments', $headers, $body);

        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
        } catch(GuzzleClientException $e) {
            $response = $e->getResponse();
        }

        $payload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() >= 400){
            throw new ErrorException(json_encode($payload));
        }

        return $payload;
    }

    protected function ApiCancelCharge($chargeId, $authToken)
    {
        $headers = [
            'Authorization' => 'Bearer '.$authToken,
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Resource-Token' => $this->privateToken,
            'X-Api-Version' => 2
        ];
        
        $request = new GuzzleRequest('PUT', JunoEnvironment::getUrl($this->environment).'charges/'.$chargeId.'/cancelation', $headers);

        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
        } catch(GuzzleClientException $e) {
            $response = $e->getResponse();
        }

        $payload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() >= 400){
            throw new ErrorException(json_encode($payload));
        }

        return $payload;
    }
    
    protected function ApiGetCharge($chargeId, $authToken)
    {
        $headers = [
            'Authorization' => 'Bearer '.$authToken,
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Resource-Token' => $this->privateToken,
            'X-Api-Version' => 2
        ];
        
        $request = new GuzzleRequest('GET', JunoEnvironment::getUrl($this->environment).'charges/'.$chargeId, $headers);

        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
        } catch(GuzzleClientException $e) {
            $response = $e->getResponse();
        }

        $payload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() >= 400){
            throw new ErrorException(json_encode($payload));
        }

        return $payload;
    }

    protected function ApiCreatePixKey(JunoPix $pixKey, $authToken)
    {
        $headers = [
            'Authorization' => 'Bearer '.$authToken,
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Resource-Token' => $this->privateToken,
            'X-Idempotency-Key' => $pixKey->getIdempotencyKey(),
            'X-Api-Version' => 2
        ];

        $body = json_encode([
            'type' => $pixKey->getType(),
            'key' => $pixKey->getKey()
        ]);

        $request = new GuzzleRequest('POST', JunoEnvironment::getUrl($this->environment).'pix/keys', $headers, $body);

        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
        } catch(GuzzleClientException $e) {
            $response = $e->getResponse();
        }

        $payload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() >= 400){
            throw new ErrorException(json_encode($payload), $response->getStatusCode());
        }

        return $payload;
    }

    protected function apiCreateWebhook($url, $authToken)
    {
        $headers = [
            'Authorization' => 'Bearer '.$authToken,
            'Content-Type' => 'application/json;charset=UTF-8',
            'X-Resource-Token' => $this->privateToken,
            'X-Api-Version' => 2
        ];

        $body = json_encode([
            'url' => $url,
            'eventTypes' => [
                "DOCUMENT_STATUS_CHANGED", "DIGITAL_ACCOUNT_STATUS_CHANGED", "TRANSFER_STATUS_CHANGED", "PAYMENT_NOTIFICATION", "CHARGE_STATUS_CHANGED"
            ]
        ]);

        $request = new GuzzleRequest('POST', JunoEnvironment::getUrl($this->environment).'notifications/webhooks', $headers, $body);

        $client = new GuzzleClient();
        try {
            $response = $client->send($request);
        } catch(GuzzleClientException $e) {
            $response = $e->getResponse();
        }

        $payload = json_decode($response->getBody()->getContents());

        if($response->getStatusCode() >= 400){
            throw new ErrorException(json_encode($payload), $response->getStatusCode());
        }

        return $payload;
    }
}