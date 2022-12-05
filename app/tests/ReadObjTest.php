<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReadObjTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/obj/read');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(true, is_array(json_decode($client->getResponse()->getContent(), true)));
    }
}
