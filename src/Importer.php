<?php

namespace ImportToPocket;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Importer
{
    const REQUESTS_LIMIT = 320;

    private $client;
    private $readerResult;
    private $accessToken;
    private $consumerKey;

    public function __construct(array $readerResult, string $accessToken, string $consumerKey)
    {
        $this->readerResult = $readerResult;
        $this->accessToken = $accessToken;
        $this->consumerKey = $consumerKey;
        $this->client = new Client();
    }

    public function add()
    {
        if (!empty($this->readerResult)) {
            $counter = 0;
            foreach ($this->readerResult as $url => $title) {
                if (self::REQUESTS_LIMIT === $counter) {
                    throw new \Exception('You have exceeded calls limit. Try to continue in an hour.');
                }
                $response = $this->client->send($this->prepareRequest($url, $title));
                if (200 !== $response->getStatusCode()) {
                    throw new \Exception('Adding request failed: Status code: ' . $response->getStatusCode());
                }

                $responseEncoded = json_decode($response->getBody()->getContents());
                echo "Item: " . $responseEncoded->item->item_id . " " . $responseEncoded->item->title . " added!\n";
                $counter++;
            }
        }
    }

    private function prepareRequest(string $url, string $title): object
    {
        $uri = 'https://getpocket.com/v3/add';
        $headers = ['Content-Type' => 'application/json; charset=UTF8', 'X-Accept' => 'application/json'];
        $body = [
            'url' => $url,
            'title' => $title,
            'consumer_key' => $this->consumerKey,
            'access_token' => $this->accessToken,
        ];

        return new Request('POST', $uri, $headers, json_encode($body));
    }
}
