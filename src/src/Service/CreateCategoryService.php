<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Request\CreateCategory;
use Doctrine\ORM\EntityManagerInterface;
use OpenTelemetry\API\Trace\TracerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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
        #[Autowire('@open_telemetry.traces.tracers.main')]
        private TracerInterface $tracer,
    ) {}

    public function create(CreateCategory $dto): Uuid
    {
        $span = $this->tracer->spanBuilder('category-service:create')->startSpan();
        if ($this->categoryRepository->findBySlug($this->slugger->slug($dto->name))) {
            $span->setAttribute('db.query.empty', true);
            $span->end();
            throw new BadRequestHttpException('Category already exists.');
        }

        $category = new Category(
            slug: $this->slugger->slug($dto->name),
            name: $dto->name,
        );

        $span->setAttribute('db.start', $category->getSlug());
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        $span->setAttribute('db.save', $category->getSlug());
        $span->end();

        $this->logger->info('Создана новая категория', context: [$category->getId()]);

        return $category->getId();
    }
}
