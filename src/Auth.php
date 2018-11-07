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
		$this->redirectUrl = ''; //TODO need to add redirectUrl
	}

	public function authenticate(): string {
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

			if ( ! empty( $responseEncoded->code ) ) {
				return $this->buildCorrectRedirectUrl( $responseEncoded->code );
			}
		} catch ( \Exception $e ) {
			echo 'Message: ' . $e->getMessage();
		}
	}

	private function buildCorrectRedirectUrl( string $requestToken ): string {
		return 'https://getpocket.com/auth/authorize?request_token=' . $requestToken . '&redirect_uri=' . $this->redirectUrl;
	}
}