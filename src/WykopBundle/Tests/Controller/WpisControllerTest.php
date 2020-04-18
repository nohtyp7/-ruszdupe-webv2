<?php

namespace WykopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WpisControllerTest extends WebTestCase
{
    public function testDodaj()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/dodaj');
    }

}
