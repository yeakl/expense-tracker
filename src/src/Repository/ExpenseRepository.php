<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Request\ExpenseListFilter;
use App\Request\Pagination;
use App\Request\TotalExpenseFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function findTotalByFilter(TotalExpenseFilter $filter): string
    {
        $query = $this
            ->createQueryBuilder('e')
            ->select('SUM(e.amount) as total')
            ->andWhere('e.currency = :currency')
            ->setParameter(':currency', $filter->currency->value);

        if ($filter->year) {
            $query->andWhere('YEAR(e.at) = :year')->setParameter(':year', $filter->year);
        }
        if ($filter->month) {
            $query->andWhere('MONTH(e.at) = :month')->setParameter(':month', $filter->month);
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    public function findByFilterAsArray(ExpenseListFilter $filter, Pagination $pagination): array
    {
        $query = $this
            ->createQueryBuilder('e')
            ->orderBy('e.at', 'DESC');

        if ($filter->category) {
            $query
                ->join('e.category', 'c')
                ->andWhere('c.slug = :category')
                ->setParameter(':category', $filter->category);
        }

        if ($filter->dateFrom) {
            $query
                ->andWhere('e.at >= :dateFrom')
                ->setParameter(':dateFrom', $filter->dateFrom . ' 00:00:00');
        }

        if ($filter->dateTo) {
            $query
                ->andWhere('e.at <= :dateTo')
                ->setParameter(':dateTo', $filter->dateTo . ' 23:59:59');
        }

        return $query
            ->getQuery()
            ->setMaxResults($pagination->perPage)
            ->setFirstResult(($pagination->page - 1) * $pagination->perPage)
            ->getArrayResult();
    }
}
