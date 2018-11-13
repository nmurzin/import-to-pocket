<?php

namespace ImportToPocket;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Importer
{
    private $client;
    private $readerResult;
    private $accessToken;
    private $consumerKey;

    public function __construct(array $readerResult, string $accessToken, string $consumerKey)
    {
        $this->readerResult = $readerResult;
        $this->accessToken = $accessToken;
        $this->consumerKey= $consumerKey;
		$this->client = new Client();
    }

    public function add()
	{
		if ( ! empty( $this->readerResult ) ) {
			foreach ( $this->readerResult as $url => $title ) {
				$response = $this->client->send( $this->prepareRequest( $url, $title ) );
				if ( 200 !== $response->getStatusCode() ) {
					throw new \Exception( 'Request failed: Status code: ' . $response->getStatusCode() );
				}
			}
		}
	}

	private function prepareRequest( string $url, string $title ): object
	{
		$uri     = 'https://getpocket.com/v3/add';
		$headers = [ 'Content-Type' => 'application/json; charset=UTF8', 'X-Accept' => 'application/json' ];
		$body    = [
			'url' => $url,
			'title' => $title,
			'consumer_key' => $this->consumerKey,
			'access_token' => $this->accessToken,
		];

		return new Request( 'POST', $uri, $headers, json_encode( $body ) );

	}
}
