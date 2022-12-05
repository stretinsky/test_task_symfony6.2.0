<?php

namespace App\Controller;

use App\Entity\Element;
use App\Service\ObjService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Stmt\Return_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class ObjController extends AbstractController
{
    /**
     * @Route("/api/obj/create", methods={"GET", "POST"})
     */
    public function create(Request $request, ObjService $objService): JsonResponse
    {
        $start = microtime(true);
        $memory = memory_get_usage();

        if ($request->getMethod() == 'GET') {
            $jsonString = $request->query->get('json');
        } else {
            $jsonString = $request->getContent();
        }

        try {
            $id = $objService->createObj($jsonString);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => true,
                'message' => $e->getMessage()
            ]);
        }

        $memory = memory_get_usage() - $memory;
        $i = 0;
        while (floor($memory / 1024) > 0) {
            $i++;
            $memory /= 1024;
        }
        
        $name = array('byte', 'k', 'mb');

       return new JsonResponse([
            'result' => round(microtime(true) - $start, 4) . "sec. \ " . round($memory, 2) . ' ' . $name[$i],
            'id' => $id
       ]);
    }

}