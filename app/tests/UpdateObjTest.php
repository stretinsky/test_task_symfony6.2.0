<?php

namespace App\Tests;

use App\Entity\Obj;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateObjTest extends WebTestCase
{
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
        $crawler = $client->request('GET', '/api/obj/update', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('404');
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

        $objs = $em->getRepository(Obj::class)->findAll();
        if (count($objs) < 0) {
            $obj = new Obj();
            $em->persist($obj);
            $em->flush();
        } else {
            $obj = $objs[0];
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/obj/update?id=' . $obj->getId() . '&json={%22test%22:%2023}', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('200');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['success']);
    }

    public function testWithBadJson(): void
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

        $objs = $em->getRepository(Obj::class)->findAll();
        if (count($objs) < 0) {
            $obj = new Obj();
            $em->persist($obj);
            $em->flush();
        } else {
            $obj = $objs[0];
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/obj/update?id=' . $obj->getId() . '&json=qweasdasd', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('400');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['error']);
    }

    public function testBody(): void
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

        $objs = $em->getRepository(Obj::class)->findAll();
        if (count($objs) < 0) {
            $obj = new Obj();
            $em->persist($obj);
            $em->flush();
        } else {
            $obj = $objs[0];
        }

        $jwt = $this->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);

        self::ensureKernelShutdown();
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/obj/update?id=' . $obj->getId(), [
            'test' => 23
        ], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt,
            'CONTENT_TYPE' => 'application/json'
        ], '{"test": 23}');

        $this->assertResponseStatusCodeSame('200');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['success']);
    }
}
