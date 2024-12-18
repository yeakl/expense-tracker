<?php

namespace App\Service;

use App\Entity\Expense;
use App\Repository\CategoryRepository;
use App\Request\CreateExpense;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateExpenseService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository,
        private LoggerInterface $logger,
    ) {}

    public function create(CreateExpense $expenseDto): Uuid
    {
        $category = null;
        if ($expenseDto->category) {
            $category = $this->categoryRepository->findBySlug($expenseDto->category);
        }

        $expense = new Expense(
            amount: $expenseDto->amount,
            currency: $expenseDto->currency,
            at: $expenseDto->date,
            description: $expenseDto->description,
            category: $category,
        );

        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        $this->logger->info('Создан новый расход', ['expense' => $expense]);
        return $expense->getId();
    }
}
