<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testSignin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/signIn');
    }

    public function testSignup()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/signUp');
    }

    public function testSignout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/signOut');
    }

}
