<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ORM\Table(name: 'book')]
#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book extends Publication {
    public function getCategory() : string {
        return self::BOOK;
    }

    public function normalize(NormalizerInterface $serializer, ?string $format = null, array $context = []): array
    {
        $results = parent::normalize($serializer, $format, $context);
        $results['recordType'] = 'Book';
        return $results;
    }
}
