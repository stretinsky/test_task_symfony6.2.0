<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController
{
    /**
     * @Route("/api/lucky", name="app_lucky")
     */
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new JsonResponse(
           ['luckyNumber' => $number]
        );
    }
}