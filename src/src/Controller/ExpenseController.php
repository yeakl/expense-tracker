<?php

namespace App\Controller;

use App\Repository\ExpenseRepository;
use App\Request\CreateExpense;
use App\Request\ExpenseListFilter;
use App\Request\Pagination;
use App\Request\TotalExpenseFilter;
use App\Service\CreateExpenseService;
use OpenTelemetry\API\Trace\TracerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route(path: '/expense')]
class ExpenseController extends AbstractController
{
    public function __construct(
        private readonly CreateExpenseService $createExpenseService,
        private readonly ExpenseRepository $expenseRepository,
        #[Autowire('@open_telemetry.traces.tracers.main')]
        private readonly TracerInterface $tracer,
    ) {}

    #[Route(path: '/', name: 'expense_route', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateExpense $expense,
    ): Response {
        $id = $this->createExpenseService->create($expense);

        return new JsonResponse([
            'id' => $id
        ]);
    }

    #[Route(path: '/total')]
    public function total(
        #[MapQueryString] TotalExpenseFilter $filter,
    ): JsonResponse
    {
        $amount = $this->expenseRepository->findTotalByFilter($filter);
        return new JsonResponse(['amount' => $amount]);
    }

    #[Route(path: '/', methods: ['GET'])]
    public function list(
        #[MapQueryString] ExpenseListFilter $filter,
        #[MapQueryString] Pagination $pagination,
    ): JsonResponse
    {
        $span = $this->tracer->spanBuilder('expense:list')->startSpan();
        $context = $span->activate();
        $expenses = $this->expenseRepository->findByFilterAsArray(
            filter: $filter,
            pagination: $pagination
        );
        $span->end();
        $context->detach();

        return new JsonResponse($expenses);
    }
}