<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CompilationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'collection')]
#[ORM\Entity(repositoryClass: CompilationRepository::class)]
class Compilation extends Publication {
    public function getCategory() : string {
        return self::COMPILATION;
    }
}
