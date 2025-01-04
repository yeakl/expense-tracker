<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/about')]
class AboutController extends AbstractController
{
    public function index(): JsonResponse
    {
        return new JsonResponse(['status' => 'success', 'data' => ['user' => 124]]);
    }
}
