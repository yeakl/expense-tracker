<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Request\CreateCategory;
use App\Service\CreateCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/category', name: 'category')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CreateCategoryService $createCategoryService,
        private readonly CategoryRepository $categoryRepository,
    ) {}

    #[Route(path: '/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateCategory $request,
    ): JsonResponse {
        $categoryId = $this->createCategoryService->create($request);
        return new JsonResponse(['id' => $categoryId]);
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->findAllAsArray();
        return new JsonResponse($categories);
    }
}