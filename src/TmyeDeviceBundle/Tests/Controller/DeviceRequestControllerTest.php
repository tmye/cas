<?php

namespace TmyeDeviceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeviceRequestControllerTest extends WebTestCase
{
    public function testIndexget()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/data/get');
    }

}
