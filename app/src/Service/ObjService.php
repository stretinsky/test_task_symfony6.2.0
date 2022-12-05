<?php

namespace App\Service;

use App\Entity\Obj;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

class ObjService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createObj(?string $data) 
    {
        if (!$this->is_json($data)) {
            throw new Exception('Invalid json string');
        }

        $obj = new Obj();
        $obj->setData(json_decode($data, true));
    
        $this->em->persist($obj);
        $this->em->flush();

        return $obj->getId();
    }

    public function readObj()
    {
        return $this->em->getRepository(Obj::class)->findAll();
    }

    public function updateObj(int $id, ?string $data)
    {
        $obj = $this->em->getRepository(Obj::class)->findOneBy(['id' => $id]);

        if ($obj == null) {
            throw new EntityNotFoundException('Obj not found', 404);
        }

        if (!$this->is_json($data)) {
            throw new Exception('Invalid json string');
        }  

        $obj->setData(json_decode($data, true));
        $this->em->flush();

        return $obj->getId();
    }

    public function deleteObj(int $id)
    {
        $obj = $this->em->getRepository(Obj::class)->findOneBy(['id' => $id]);

        if ($obj == null) {
            throw new EntityNotFoundException('Obj not found', 404);
        }

        $this->em->remove($obj);
        $this->em->flush();
    }

    private function is_json($string) {
        return !empty($string) && is_string($string) && is_array(json_decode($string, true)) && json_last_error() == 0;
    }
}
