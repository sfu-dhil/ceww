<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompilationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ORM\Table(name: 'collection')]
#[ORM\Entity(repositoryClass: CompilationRepository::class)]
class Compilation extends Publication {
    public function getCategory() : string {
        return self::COMPILATION;
    }

    public function normalize(NormalizerInterface $serializer, ?string $format = null, array $context = []): array
    {
        $results = parent::normalize($serializer, $format, $context);
        $results['recordType'] = 'Compilation';
        return $results;
    }
}
