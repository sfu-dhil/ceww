<?php

declare(strict_types=1);

namespace App\Entity;

use ReflectionClass;

trait HasHighlights {
    private array $highlights;

    public function getEntityClassName() : string {
        return (new ReflectionClass($this))->getShortName();
    }

    public function setElasticHighlights(array $highlights) : self {
        $this->highlights = $highlights;

        return $this;
    }

    public function getElasticHighlights() : array {
        return $this->highlights;
    }
}
