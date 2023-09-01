<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PeriodicalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'periodical')]
#[ORM\Entity(repositoryClass: PeriodicalRepository::class)]
class Periodical extends Publication {
    #[ORM\Column(type: Types::STRING, length: 48, nullable: true)]
    private ?string $runDates = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $continuedFrom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $continuedBy = null;

    public function setRunDates(?string $runDates) : self {
        $this->runDates = $runDates;

        return $this;
    }

    public function getRunDates() : ?string {
        return $this->runDates;
    }

    public function setContinuedFrom(?string $continuedFrom) : self {
        $this->continuedFrom = $continuedFrom;

        return $this;
    }

    public function getContinuedFrom() : ?string {
        return $this->continuedFrom;
    }

    public function setContinuedBy(?string $continuedBy) : self {
        $this->continuedBy = $continuedBy;

        return $this;
    }

    public function getContinuedBy() : ?string {
        return $this->continuedBy;
    }

    public function getCategory() : string {
        return self::PERIODICAL;
    }
}
