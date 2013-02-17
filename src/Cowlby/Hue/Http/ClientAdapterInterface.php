<?php

namespace Cowlby\Hue\Http;

interface ClientAdapterInterface
{
    /**
     * Perform a GET request to the specified relative uri with the given body
     * and return a hydrated entity.
     *
     * @param string $uri The relative uri to GET.
     * @param mixed $body Optional message body.
     * @return mixed The response or a hydrated object.
     */
    public function get($uri, $body = NULL);

    /**
     * Perform a PUT request to the specified relative uri with the given body
     * and return a hydrated entity.
     *
     * @param string $uri The relative uri to PUT.
     * @param mixed $body Optional message body.
     * @return mixed The response or a hydrated object.
     */
    public function put($uri, $body = NULL);
}
