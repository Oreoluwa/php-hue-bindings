<?php

namespace Cowlby\Hue\Http;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Message\RequestInterface;

class GuzzleClientAdapter implements ClientAdapterInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param ClientInterface $client The Guzzle HTTP client to use.
     */
    public function __construct(ClientInterface $client)
    {
        $this->setClient($client);
    }

    /**
     * Sets the internal Guzzle HTTP client to make requests with.
     *
     * @param ClientInterface $client
     * @return \Cowlby\Hue\Http\ClientAdapterInterface
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get($uri, $body = NULL)
    {
        $request = $this->client->get($uri, NULL, $body);
        return $this->send($request);
    }

    /**
     * Sends the passed Request and returns the response or a hydrated object.
     *
     * @param RequestInterface $request The Request to send.
     * @return mixed The response or a hydrated object.
     */
    protected function send(RequestInterface $request)
    {
        $response = $this->client->send($request);

        $body = $response->getBody();

        return $body;
    }
}
