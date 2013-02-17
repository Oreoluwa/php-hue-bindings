<?php

namespace Cowlby\Hue;

use Cowlby\Hue\Http\ClientAdapterInterface;
use Symfony\Component\DomCrawler\Crawler;

class BridgeManager implements BridgeManagerInterface
{
    const SSDP_IP = '239.255.255.250';
    const SSDP_PORT = 1900;
    const SSDP_BRIDGE_PATTERN = '/LOCATION: (http:\/\/([^:]+):([^\/]+)\/description\.xml)/m';

    protected $client;

    public function __construct(ClientAdapterInterface $client)
    {
        $this->client = $client;
    }

    public function register($username)
    {
        return;
    }

    public function discover($timeout = 2)
    {
        $ssdpPacket = "M-SEARCH * HTTP/1.1\r\n";
        $ssdpPacket .= "Host: 239.255.255.250:1900\r\n";
        $ssdpPacket .= "Man: \"ssdp:discover\"\r\n";
        $ssdpPacket .= "ST:upnp:rootdevice\r\n";
        $ssdpPacket .= "MX:3\r\n";
        $ssdpPacket .= "\r\n";

        // UPnP discovery.
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_sendto($sock, $ssdpPacket, strlen($ssdpPacket), 0, self::SSDP_IP, self::SSDP_PORT);

        $startTime = time();
        $responses = array();
        while (time() - $startTime < $timeout) {

            @socket_recv($sock, $data, 1024, MSG_DONTWAIT);
            $data = trim($data);

            if (empty($data)) {
                usleep(10000);
            } else {
                $responses[] = $data;
            }
        }

        socket_close($sock);

        // Find all bridge-like devices.
        $services = array();
        foreach ($responses as $response) {
            if (preg_match(self::SSDP_BRIDGE_PATTERN, $response, $matches)) {
                $services[$matches[2]][$matches[3]] = $matches[1];
            }
        }

        // Query services and find philips hue devices.
        $bridges = array();
        foreach ($services as $ip => $service) {
            foreach ($service as $port => $location) {

                $response = $this->client->get($location);

                $crawler = new Crawler();
                $crawler->addContent($response, 'xml');

                $modelName = $crawler->filterXPath('//device/modelName');
                if (count($modelName) > 0 && preg_match('/Philips hue bridge/i', $modelName->text())) {

                    $bridge = array(
                        'ip' => $ip,
                        'port' => $port
                    );

                    foreach ($crawler->filterXPath('//device')->children() as $domElement) {
                        if (in_array($domElement->nodeName, array('deviceType', 'friendlyName', 'modelName', 'modelNumber', 'serialNumber', 'UDN'))) {
                            $bridge[$domElement->nodeName] = $domElement->nodeValue;
                        }
                    }

                    $bridges[$bridge['UDN']] = $bridge;
                }
            }
        }

        return $bridges;
    }
}

