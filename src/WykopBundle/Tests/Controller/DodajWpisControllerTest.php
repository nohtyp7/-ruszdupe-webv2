<?php

namespace WykopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DodajWpisControllerTest extends WebTestCase
{
    public function testDodajwpis()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/dodajWpis');
    }

}
