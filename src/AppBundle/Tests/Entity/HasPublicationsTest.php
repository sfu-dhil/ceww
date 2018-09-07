<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\HasPublications;
use AppBundle\Entity\Publication;
use AppBundle\DataFixtures\ORM\LoadBook;
use AppBundle\DataFixtures\ORM\LoadCompilation;
use AppBundle\DataFixtures\ORM\LoadPeriodical;
use Nines\UtilBundle\Tests\Util\BaseTestCase;


class HasPublicationsTest extends BaseTestCase {
    
    protected function getFixtures() {
        return array(
            LoadBook::class,
            LoadCompilation::class,
            LoadPeriodical::class,
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
