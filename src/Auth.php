<?php
/**
 * Proceed authentication
 */
namespace ImportToPocket;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class Auth
 * @package ImportToPocket
 */
class Auth
{
	/** @var object */
    private $client;
    /** @var string  */
    private $consumerKey;
    /** @var string  */
    private $redirectUrl;
    /** @var string */
    private $requestToken;

	/**
	 * Auth constructor.
	 */
    public function __construct()
    {
        $this->client = new Client();
        $this->consumerKey = getenv('POCKET_CONSUMER_KEY');
        $this->redirectUrl = 'http://0.0.0.0/serve.php';
    }

	/**
	 * @return string
	 * @throws \Exception
	 */
    public function authenticate(): string
    {
        if (!empty($this->requestToken) && $accessToken = $this->obtainAccessToken($this->requestToken)) {
            return $accessToken;
        }

        throw new \Exception('You must authorize this app here:' . $this->buildAuthUrl($this->requestToken));
    }

	/**
	 * @return string
	 */
    public function authorize(): string
    {
        $this->requestToken = $this->obtainRequestToken();

        return $this->buildAuthUrl($this->requestToken);
    }

	/**
	 * @return string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
    private function obtainRequestToken(): string
    {
        try {
            $uri = 'https://getpocket.com/v3/oauth/request';
            $headers = ['Content-Type' => 'application/json; charset=UTF8', 'X-Accept' => 'application/json'];
            $body = [
                'consumer_key' => $this->consumerKey,
                'redirect_uri' => $this->redirectUrl
            ];

            $response = $this->client->send(new Request('POST', $uri, $headers, json_encode($body)));

            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Request failed: Status code: ' . $response->getStatusCode());
            }

            $responseEncoded = json_decode($response->getBody()->getContents());

            if (empty($responseEncoded->code)) {
                throw new \Exception('Bad response!');
            }

            return $responseEncoded->code;
        } catch (\Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }
    }

	/**
	 * @param string $requestToken
	 *
	 * @return string
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
    private function obtainAccessToken(string $requestToken): string
    {
        try {
            $uri = 'https://getpocket.com/v3/oauth/authorize';
            $headers = ['Content-Type' => 'application/json; charset=UTF8', 'X-Accept' => 'application/json'];
            $body = [
                'consumer_key' => $this->consumerKey,
                'code' => $requestToken
            ];

            $response = $this->client->send(new Request('POST', $uri, $headers, json_encode($body)));

            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Request failed: Status code: ' . $response->getStatusCode());
            }

            $responseEncoded = json_decode($response->getBody()->getContents());

            if (empty($responseEncoded->access_token)) {
                throw new \Exception('Access token not exist: ' . $response->getBody()->getContents());
            }

            return $responseEncoded->access_token;
        } catch (\Exception $e) {
            echo 'Message: ' . $e->getMessage();
        }


    }

	/**
	 * @param string $requestToken
	 *
	 * @return string
	 */
    private function buildAuthUrl(string $requestToken): string
    {
        return 'https://getpocket.com/auth/authorize?request_token=' . $requestToken . '&redirect_uri=' . $this->redirectUrl;
    }
}
