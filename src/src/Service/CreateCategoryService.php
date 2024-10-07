<?php

namespace App\Service;

use App\Entity\Category;
use App\Request\CreateCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateCategoryService
{
    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $entityManager,
    ) {}

    public function create(CreateCategory $dto): Uuid
    {
        $category = new Category(
            slug: $this->slugger->slug($dto->name),
            name: $dto->name,
        );

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category->getId();
    }
}
