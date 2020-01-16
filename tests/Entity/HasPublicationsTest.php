<?php

namespace App\Tests\Entity;

use App\DataFixtures\BookFixtures;
use App\DataFixtures\CompilationFixtures;
use App\DataFixtures\PeriodicalFixtures;
use App\Entity\HasPublications;
use App\Entity\Publication;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class HasPublicationsTest extends BaseTestCase {
    protected function fixtures() : array {
        return array(
            BookFixtures::class,
            CompilationFixtures::class,
            PeriodicalFixtures::class,
        );
    }

    public function testAddPublication() {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $this->assertEquals(1, count($mock->getPublications()));
    }

    public function testDuplicatePublication() {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $mock->addPublication($this->references->getReference('book.1'));
        $this->assertEquals(1, count($mock->getPublications()));
    }

    public function testGetPublications() {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $mock->addPublication($this->references->getReference('compilation.1'));
        $this->assertEquals(2, count($mock->getPublications()));
    }

    public function testGetPublicationsCategory() {
        $mock = $this->getMockForTrait(HasPublications::class);
        $mock->addPublication($this->references->getReference('book.1'));
        $mock->addPublication($this->references->getReference('compilation.1'));
        $this->assertEquals(1, count($mock->getPublications(Publication::BOOK)));
    }
}
