<?php

namespace App\Tests;

use App\Entity\Obj;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteObjTest extends WebTestCase
{
    public function testBadId(): void
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
        $crawler = $client->request('GET', '/api/obj/delete?id=dfsasddfs', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('404');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['error']);
    }

    public function testGoodId(): void
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
        $crawler = $client->request('GET', '/api/obj/delete?id=' . $obj->getId(), [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('200');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['success']);
    }

    public function testNullId(): void
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
        $crawler = $client->request('GET', '/api/obj/delete', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $jwt
        ]);

        $this->assertResponseStatusCodeSame('404');
        $this->assertEquals(true, json_decode($client->getResponse()->getContent(), true)['error']);
    }
}
