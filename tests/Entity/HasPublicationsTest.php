<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\DataFixtures\BookFixtures;
use App\DataFixtures\CompilationFixtures;
use App\DataFixtures\PeriodicalFixtures;
use App\Entity\HasPublications;
use App\Entity\Publication;
use Nines\UtilBundle\Tests\BaseCase;

class HasPublicationsTest extends BaseCase {
    protected function fixtures() : array {
        return [
            BookFixtures::class,
            CompilationFixtures::class,
            PeriodicalFixtures::class,
        ];
    }

    public function testAddPublication() : void {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $this->assertCount(1, $mock->getPublications());
    }

    public function testDuplicatePublication() : void {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $mock->addPublication($this->references->getReference('book.1'));
        $this->assertCount(1, $mock->getPublications());
    }

    public function testGetPublications() : void {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $mock->addPublication($this->references->getReference('compilation.1'));
        $this->assertCount(2, $mock->getPublications());
    }

    public function testGetPublicationsCategory() : void {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $mock->addPublication($this->references->getReference('compilation.1'));
        $this->assertCount(1, $mock->getPublications(Publication::BOOK));
    }
}
