<?php

namespace ImportToPocket;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Auth {
	private $client;
	private $consumerKey;
	private $redirectUrl;

	public function __construct() {
		$this->client      = new Client();
		$this->consumerKey = getenv( 'POCKET_CONSUMER_KEY' );
		$this->redirectUrl = 'http://0.0.0.0/serve-test.php'; //TODO need to add redirectUrl
	}

	public function authenticate() : string
    {
        $requestToken = $this->obtainRequestToken();
        if($accessToken = $this->obtainAccessToken($requestToken)){
            return $accessToken;
        }

        throw new \Exception('You must authorize this app here:' . $this->buildAuthUrl($requestToken) );
	}

	private function obtainRequestToken() : string
    {
        try {
            $uri     = 'https://getpocket.com/v3/oauth/request';
            $headers = [ 'Content-Type' => 'application/json; charset=UTF8', 'X-Accept' => 'application/json' ];
            $body    = [
                'consumer_key' => $this->consumerKey,
                'redirect_uri' => $this->redirectUrl
            ];

            $response = $this->client->send( new Request( 'POST', $uri, $headers, json_encode( $body ) ) );

            if ( 200 !== $response->getStatusCode() ) {
                throw new \Exception( 'Request failed: Status code: ' . $response->getStatusCode() );
            }

            $responseEncoded = json_decode( $response->getBody()->getContents() );

            if ( empty( $responseEncoded->code ) ) {
                throw new \Exception( 'Bad response!' );
            }

            return $responseEncoded->code;
        } catch ( \Exception $e ) {
            echo 'Message: ' . $e->getMessage();
        }
    }

    private function obtainAccessToken(string $requestToken) : string
    {
        try {
            $uri     = 'https://getpocket.com/v3/oauth/authorize';
            $headers = [ 'Content-Type' => 'application/json; charset=UTF8', 'X-Accept' => 'application/json' ];
            $body    = [
                'consumer_key' => $this->consumerKey,
                'code'         => $requestToken
            ];

            $response = $this->client->send( new Request( 'POST', $uri, $headers, json_encode( $body ) ) );

            if ( 200 !== $response->getStatusCode() ) {
                throw new \Exception( 'Request failed: Status code: ' . $response->getStatusCode() );
            }

            $responseEncoded = json_decode( $response->getBody()->getContents() );

            if(!empty($responseEncoded->acess_token) && !empty($responseEncoded->username)){
                return $responseEncoded->acess_token;
            }
        } catch ( \Exception $e ) {
            echo 'Message: ' . $e->getMessage();
        }
    }

	private function buildAuthUrl( string $requestToken ): string {
		return 'https://getpocket.com/auth/authorize?request_token=' . $requestToken . '&redirect_uri=' . $this->redirectUrl;
	}
}