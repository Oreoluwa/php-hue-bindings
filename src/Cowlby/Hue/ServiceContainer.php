<?php

namespace Cowlby\Hue;

use Pimple;
use Guzzle\Http\Client;
use Cowlby\Hue\Http\GuzzleClientAdapter;

class ServiceContainer extends Pimple
{
    public function __construct($bridgeUsername, $bridgeIp, $bridgePort = 80)
    {
        $this['bridge.username'] = $bridgeUsername;
        $this['bridge.ip'] = $bridgeIp;
        $this['bridge.port'] = $bridgePort;

        $this['ssdp.ip'] = '239.255.255.250';
        $this['ssdp.port'] = 1900;
        $this['ssdp.timeout'] = 2;
        $this['ssdp.bridge_pattern'] = '/LOCATION: (http:\/\/([^:]+):([^\/]+)\/description\.xml)/m';
        $this['ssdp.packet'] = 'M-SEARCH * HTTP/1.1\r\n';
        $this['ssdp.packet'] .= 'Host: ' . $this['ssdp.ip'] . ':' . $this['ssdp.port'] . '\r\n';
        $this['ssdp.packet'] .= 'Man: "ssdp:discover"\r\n';
        $this['ssdp.packet'] .= 'ST:upnp:rootdevice\r\n';
        $this['ssdp.packet'] .= 'MX:3\r\n';
        $this['ssdp.packet'] .= '\r\n';

        $this['guzzle'] = $this->share(function($container) {

            $client = new Client('http://{bridgeIp}:{bridgePort}/api', array(
                'bridgeIp' => $container['bridge.ip'],
                'bridgePort' => $container['bridge.port'],
                'username' => $container['bridge.username']
            ));

            return $client;
        });

        $this['client'] = $this->share(function($container) {
            return new GuzzleClientAdapter($container['guzzle']);
        });

        $this['bridge_manager'] = $this->share(function($container) {
            return new BridgeManager($container['client'], $container['ssdp.timeout']);
        });

        $this['light_manager'] = $this->share(function($container) {
            return new LightManager($container['client']);
        });
    }

    /**
     * @return \Cowlby\Hue\BridgeManagerInterface
     */
    public function getBridgeManager()
    {
        return $this['bridge_manager'];
    }

    /**
     * @return \Cowlby\Hue\LightManagerInterface
     */
    public function getLightManager()
    {
        return $this['light_manager'];
    }
}
