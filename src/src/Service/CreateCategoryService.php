<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Request\CreateCategory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateCategoryService
{
    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private CategoryRepository $categoryRepository,
    ) {}

    public function create(CreateCategory $dto): Uuid
    {
        if ($this->categoryRepository->findBySlug($this->slugger->slug($dto->name))) {
            $this->logger->error('Попытка записать существующую категорию', ['category' => $dto->name]);
            throw new BadRequestHttpException('Category already exists.');
        }

        $category = new Category(
            slug: $this->slugger->slug($dto->name),
            name: $dto->name,
        );

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->logger->info('Создана новая категория', context: [$category->getId()]);

        return $category->getId();
    }
}
