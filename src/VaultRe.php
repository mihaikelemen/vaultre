<?php

namespace MihaiKelemen\VaultRe;

use GuzzleHttp\Client;

/**
 * VaultRE API abstraction
 * 
 * @link   https://docs.api.vaultre.com.au/
 * @author mihai@webmanage.ro
 */
class VaultRe
{

    /**
     * ValutRE API endpoint
     * 
     * @var string
     */
    private $endpoint = 'https://ap-southeast-2.api.vaultre.com.au/api/v1.3/';

    /**
     * Stores the errors
     * 
     * @var mixed
     */
    private $error;

    /**
     * Stores successfull api returned data
     * 
     * @var array
     */
    private $response = [];

    /**
     * Number of items to retrive on a simple call
     * 
     * @var integer
     */
    private $page_size = 100;

    /**
     * Current page
     * 
     * @var integer
     */
    private $page = 1;

    /**
     * API methods to handle payloads
     * 
     * @var array
     */
    private $methods = ['fetch' => 'GET', 'add' => 'POST', 'delete' => 'DELETE', 'update' => 'PUT'];

    /**
     * @param string $key   API key
     * @param string $token Auth token
     */
    public function __construct(string $api_key, string $token)
    {
        $this->api_key = $api_key;
        $this->token = $token;
    }

    /**
     * Set the type of data to handle.
     * 
     * @param  string $attribute 
     * @return VaultRe 
     */
    public function setAttribute(string $attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Updates the number of items displayed on a page
     * 
     * @param  int $number 
     * @return VaultRe 
     */
    public function setPageSize(int $number)
    {
        $this->page_size = $number;
        return $this;
    }

    /**
     * Set the page number to be retrived
     * 
     * @param int $number 
     * @return VaultRe 
     */
    public function setPage(int $number)
    {
        $this->page = $number;
        return $this;
    }

    /**
     * Check the presence of errors
     * 
     * @return bool 
     */
    public function isSuccess()
    {
        return \strlen($this->error) === 0;
    }

    /**
     * Retrive the error log
     * 
     * @return string 
     */
    public function errors()
    {
        return $this->error;
    }

    /**
     * Retrieve the properties sent by VaultRE
     * 
     * @return array
     */
    public function properties()
    {
        return isset($this->response['items']) ? $this->response['items'] : [];
    }

    /**
     * Store information related to the payload retrived from ValutRE: total items, total pages and pagination urls: current (self), next, previous
     * 
     * @return array 
     */
    public function pagination()
    {
        return isset($this->response['totalItems']) ? ['total_items' => $this->response['totalItems'], 'total_pages' => $this->response['totalPages'],
        'navigation' => $this->response['urls']] : [];
    }

    /**
     * Returns API response data
     * 
     * @return array 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 
     * @param  string $action    one of the available methods: fetch, update, delete, update
     * @param  array  $arguments 
     * @return VaultRe 
     * @throws Exception 
     */
    public function __call(string $action, $arguments)
    {
        if (\in_array($action, \array_keys($this->methods))) {
            $this->send($this->methods[$action], $arguments);
            return $this;
        }

        throw new \Exception(\sprintf("Undefined '%s' method!", $action), 1);
    }

    /**
     * Makes the actual call to the VaultRE endpoint.
     *
     * @throws \Exception
     * @return void
     */
    protected function send(string $method, array $args = [])
    {
        $this->error = null;
        $this->items = [];
        $this->pagination = [];

        $payload = [];

        try {

            if (\is_null($this->attribute) || empty($this->attribute)) {
                throw new \Exception('Missing critical URL component.');
            }

            $url = $this->attribute;

            if (isset($args[0])) {
                $url .= \sprintf('/%s', $args[0]);
            }

            $url .= \sprintf('%spagesize=%d&page=%d', (\strpos($url, '?') ? '&' : '?'), $this->page_size, $this->page);

            if (isset($args[1]) && \is_array($args[1])) {
                $payload = ['json' => $args[1]];
            } 

            $client = new Client([
                'base_uri' => $this->endpoint,
                'headers' => [
                    'X-Api-Key' => $this->api_key,
                    'Authorization' => \sprintf('Bearer %s', $this->token)
                ]
            ]);

            $response = $client->request($method, $url, $payload);

            $this->handleResponse($response);
        } catch (\Throwable $e) {
            $this->error = \sprintf('Error %d - %s', $e->getCode(), $e->getMessage());
        }
    }
    
    /**
     * Processes the response provided by the endpoint
     *
     * @param  \GuzzleHttp\Psr7\Response $response instance of Guzzle http client response.
     * @return void
     */
    private function handleResponse(\GuzzleHttp\Psr7\Response $response)
    {
        $data = \json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $this->response = $data;
        } else {
            $this->error = \sprintf('Error %d - %s', $response->getStatusCode(), $data['msg']);
        }
    }

}
