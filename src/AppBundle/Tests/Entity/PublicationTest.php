<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Contribution;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Publication;
use PHPUnit_Framework_TestCase;

class PublicationTest extends PHPUnit_Framework_TestCase {
    
    public function testAddGenre() {
        $publication = new Publication();
        $genre = new Genre();
        $publication->addGenre($genre);
        $publication->addGenre($genre);
        $this->assertEquals(1, count($publication->getGenres()));
    }
    
    public function testAddContribution() {
        $publication = new Publication();
        $genre = new Contribution();
        $publication->addContribution($genre);
        $publication->addContribution($genre);
        $this->assertEquals(1, count($publication->getContributions()));
    }
}
 