<?php

namespace Cowlby\Hue;

use Cowlby\Hue\Entity\Light;
use Cowlby\Hue\Http\ClientAdapterInterface;

class LightManager implements LightManagerInterface
{
    protected $client;

    public function __construct(ClientAdapterInterface $client)
    {
        $this->client = $client;
    }

    public function findAll()
    {
        $uri = '{username}/lights';
        $response = $this->client->get($uri);

        return json_decode($response, true);
    }

    public function find($id)
    {
        $uri = '{username}/lights/' . $id;
        $response = $this->client->get($uri);

        return json_decode($response, true);
    }

    public function update(Light $light, $transitionTime = null)
    {
        return $this->changeState($light->getId(), array(
            'bri' => $light->getBrightness(),
            'hue' => $light->getHue(),
            'transitiontime' => $transitionTime
        ));
    }

    public function turnOn(Light $light)
    {
        return $this->changeState($light->getId(), array('on' => true));
    }

    public function turnOff(Light $light)
    {
        return $this->changeState($light->getId(), array('on' => false));
    }

    public function changeState($id, array $state)
    {
        $uri = '{username}/lights/' . $id . '/state';
        print_r(json_encode($state));
        $response = $this->client->put($uri, json_encode($state));

        return $response;
    }
}
