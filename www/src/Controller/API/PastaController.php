<?php

namespace App\Controller\API;

use App\Repository\PastaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api/pasta", name="api.pasta.")
 */
class PastaController extends AbstractController
{

    public function __construct()
    {
    }
    
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function listAction(PastaRepository $pastaRepository): Response
    {
        return $this->response($pastaRepository->findAll());
    }

    /**
     * Returns a JSON response
     *
     * @param mixed $data
     * @param int $status
     * @param array<mixed> $headers
     * @return JsonResponse
     */
    public function response(mixed $data, int $status = Response::HTTP_OK, array $headers = []): JsonResponse
    {



        return new JsonResponse($data, $status, $headers);
    }


}