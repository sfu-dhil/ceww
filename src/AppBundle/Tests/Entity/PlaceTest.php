<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Book;
use AppBundle\Entity\Periodical;
use AppBundle\Entity\Person;
use AppBundle\Entity\Place;
use PHPUnit_Framework_TestCase;

class PlaceTest extends PHPUnit_Framework_TestCase {
    
    public function testAddPeopleBorn() {
        $place = new Place();
        $person = new Person();
        
        $place->addPersonBorn($person);
        $place->addPersonBorn($person);
        $this->assertCount(1, $place->getPeopleBorn());
    }
     
    public function testAddPeopleDied() {
        $place = new Place();
        $person = new Person();
        
        $place->addPersonDied($person);
        $place->addPersonDied($person);
        $this->assertCount(1, $place->getPeopleDied());
    }
    
    public function testGetPeopleBorn() {
        $place = new Place();
        foreach(['a' => 1950, 'b' => 1930, 'c' => 1970, 'd' => null, 'e' => 1960, 'f' => null] as $name => $year) {
            $person = new Person();
            $person->setFullName($name);
            if($year !== null) {
                $person->setBirthDate($year);
            }
            $place->addPersonBorn($person);
        }
        $people = $place->getPeopleBorn();
        $this->assertCount(6, $people);
        $this->assertEquals('d', $people[0]->getFullName());
        $this->assertEquals('f', $people[1]->getFullName());
        $this->assertEquals('b', $people[2]->getFullName());
        $this->assertEquals('a', $people[3]->getFullName());
        $this->assertEquals('e', $people[4]->getFullName());
        $this->assertEquals('c', $people[5]->getFullName());
    }
    
    public function testGetPeopleDied() {
        $place = new Place();
        foreach(['a' => 1950, 'b' => 1930, 'c' => 1970, 'd' => null, 'e' => 1960, 'f' => null] as $name => $year) {
            $person = new Person();
            $person->setFullName($name);
            if($year !== null) {
                $person->setBirthDate($year);
            }
            $place->addPersonDied($person);
        }
        $people = $place->getPeopleDied();
        $this->assertCount(6, $people);
        $this->assertEquals('d', $people[0]->getFullName());
        $this->assertEquals('f', $people[1]->getFullName());
        $this->assertEquals('b', $people[2]->getFullName());
        $this->assertEquals('a', $people[3]->getFullName());
        $this->assertEquals('e', $people[4]->getFullName());
        $this->assertEquals('c', $people[5]->getFullName());
    }
    
    public function testAddResident() {
        $place = new Place();
        $person = new Person();
        
        $place->addResident($person);
        $place->addResident($person);
        $this->assertCount(1, $place->getResidents());
    }
    
    public function testGetResidents() {
        $place = new Place();
        foreach(['b', 'd', 'a', 'c'] as $name) {
            $person = new Person();
            $person->setFullName($name);
            $person->setSortableName($name);
            $place->addResident($person);
        }
        $people = $place->getResidents();
        $this->assertCount(4, $people);
        $this->assertEquals('a', $people[0]->getFullName());
        $this->assertEquals('b', $people[1]->getFullName());
        $this->assertEquals('c', $people[2]->getFullName());
        $this->assertEquals('d', $people[3]->getFullName());
    }
    
    public function testAddPublication() {
        $place = new Place();
        $publication = new Book();
        
        $place->addPublication($publication);
        $place->addPublication($publication);
        $this->assertCount(1, $place->getPublications());
    }
    
    public function testGetPublications() {
        $place = new Place();
        foreach(['b', 'd', 'a', 'c'] as $name) {
            $publication = new Periodical();
            $publication->setTitle($name);
            $publication->setSortableTitle($name);
            $place->addPublication($publication);
        }
        $publications = $place->getPublications();
        $this->assertCount(4, $publications);
        $this->assertEquals('a', $publications[0]->getTitle());
        $this->assertEquals('b', $publications[1]->getTitle());
        $this->assertEquals('c', $publications[2]->getTitle());
        $this->assertEquals('d', $publications[3]->getTitle());
    }
}    