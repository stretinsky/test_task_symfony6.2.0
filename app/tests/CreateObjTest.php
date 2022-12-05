<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateObjTest extends WebTestCase
{
    public function testWithoutToken(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/obj/create');

        $this->assertResponseStatusCodeSame('401');
    }

    public function testWithTokenNoBody(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'admin']);
        if ($user == null) {
            $user = new User();
            $user->setEmail('admin');
            $em->persist($user);
            $em->flush();
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/obj/create', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('400');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['error']);
    }

    public function testWithTokenQuery(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'admin']);
        if ($user == null) {
            $user = new User();
            $user->setEmail('admin');
            $em->persist($user);
            $em->flush();
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/obj/create?json={%22test%22:%2023}', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('200');
        $this->assertEquals(true, is_int(json_decode($client->getResponse()->getContent(), true)['id']));
    }

    public function testWithTokenBody(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'admin']);
        if ($user == null) {
            $user = new User();
            $user->setEmail('admin');
            $em->persist($user);
            $em->flush();
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/obj/create?json={%22test%22:%2023}', [
            'test' => 23
        ], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt,
            'CONTENT_TYPE' => 'application/json'
        ], '{"test": 23}');

        $this->assertResponseStatusCodeSame('200');
        $this->assertEquals(true, is_int(json_decode($client->getResponse()->getContent(), true)['id']));
    }

    public function testWithTokenBadBody(): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'admin']);
        if ($user == null) {
            $user = new User();
            $user->setEmail('admin');
            $em->persist($user);
            $em->flush();
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/obj/create?json={%22test%22:%2023}', [
            'test' => 23
        ], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt,
            'CONTENT_TYPE' => 'application/json'
        ], '{asdasdasdasd');

        $this->assertResponseStatusCodeSame('400');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['error']);
    }
}
