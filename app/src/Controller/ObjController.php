<?php

namespace App\Controller;

use App\Service\ObjService;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
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
            ], 400);
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

    /**
     * @Route("/api/obj/read", name="api_obj_read")
     */
    public function read(ObjService $objService, SerializerInterface $serializer)
    {
        $resp = new Response($serializer->serialize($objService->readObj(), 'json'));
        $resp->headers->set('Content-Type', 'application/json');
        return $resp;
    }

    /**
     * @Route("/api/obj/update", name="api_obj_update")
     */
    public function update(ObjService $objService, Request $request)
    {
        $id = $request->query->get('id');
        if ($request->getMethod() == 'GET') {
            $jsonString = $request->query->get('json');
        } else {
            $jsonString = $request->getContent();
        }

        try {
            $objService->updateObj($id, $jsonString);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse([
                'error' => true,
                'message' => $e->getMessage()
            ], $e->getCode());
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => true,
                'message' => $e->getMessage()
            ], 400);
        }
        
        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/api/obj/delete", name="api_obj_delete")
     */
    public function delete(ObjService $objService, Request $request)
    {
        $id = $request->query->get('id');

        try {
            $objService->deleteObj($id);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse([
                'error' => true,
                'message' => $e->getMessage()
            ], $e->getCode());
        } 
        
        return new JsonResponse(['success' => true]);
    }

}