<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Request\CreateCategory;
use App\Service\CreateCategoryService;
use FriendsOfOpenTelemetry\OpenTelemetryBundle\Instrumentation\Attribute\Traceable;
use OpenTelemetry\API\Trace\TracerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route(path: '/category', name: 'category')]
#[Traceable]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CreateCategoryService $createCategoryService,
        private readonly CategoryRepository $categoryRepository,
        #[Autowire('@open_telemetry.traces.tracers.main')]
        private readonly TracerInterface $tracer,
    ) {}

    #[Route(path: '/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateCategory $request,
    ): JsonResponse {
        $span = $this->tracer->spanBuilder('category:create')->startSpan();
        $scope = $span->activate();
        try {
            $categoryId = $this->createCategoryService->create($request);
        } catch (Throwable $exception) {
            $span->setAttribute('error', true);
            $span->setAttribute('error.message', $exception->getMessage());
            throw $exception;
        } finally {
            $span->end();
            $scope->detach();
        }
        return new JsonResponse(['id' => $categoryId]);
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $categories = $this->categoryRepository->findAllAsArray();
        return new JsonResponse($categories);
    }
}