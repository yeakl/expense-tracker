<?php

namespace App\Entity;

use App\Enum\Currency;
use App\Repository\ExpenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    public function __construct(
        #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
        private string $amount,
        #[ORM\Column(length: 3)]
        private Currency $currency,
        #[ORM\Column]
        private ?\DateTimeImmutable $at,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $description = null,
        #[ORM\ManyToOne(targetEntity: Category::class)]
        #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true)]
        private ?Category $category = null,
    ) {}

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAt(): ?\DateTimeImmutable
    {
        return $this->at;
    }


    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
}
