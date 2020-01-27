<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\DateYear;
use App\Entity\Periodical;
use App\Entity\Person;
use App\Entity\Place;
use PHPUnit\Framework\TestCase;

class PlaceTest extends TestCase {
    /**
     * @dataProvider GetNameData
     *
     * @param mixed $testPlace
     * @param mixed $expectedName
     */
    public function testGetName($testPlace, $expectedName) : void {
        $place = new Place();
        $place->setName($testPlace);
        $this->assertSame($expectedName, $place->getName());
    }

    public function getNameData() {
        return [
            ['/Vancouver', '/Vancouver'],
            ['[Vancouver', '[Vancouver'],
            [']Vancouver', ']Vancouver'],
            ['Va?ncouver', 'Va?ncouver'],
            ['**Vancouver', '**Vancouver'],
            ['^Vancouver', '^Vancouver'],
            ['?Vancouver', 'Vancouver'],
            [',Vancouver', 'Vancouver'],
            [' Vancouver', 'Vancouver'],
        ];
    }

    public function testAppendNote() : void {
        $testNote = 'This is a note to append.';

        $place = new Place();

        $place->appendNote($testNote);

        $this->assertSame($testNote, $place->getNotes());
    }

    public function testAddPeopleBorn() : void {
        $place = new Place();
        $person = new Person();

        $place->addPersonBorn($person);
        $place->addPersonBorn($person);
        $this->assertCount(1, $place->getPeopleBorn());
    }

    public function testAddPeopleDied() : void {
        $place = new Place();
        $person = new Person();

        $place->addPersonDied($person);
        $place->addPersonDied($person);
        $this->assertCount(1, $place->getPeopleDied());
    }

    public function testGetPeopleBorn() : void {
        $place = new Place();
        foreach (['a' => 1950, 'b' => 1930, 'c' => 1970, 'd' => null, 'e' => 1960, 'f' => null] as $name => $year) {
            $person = new Person();
            $person->setFullName($name);
            if (null !== $year) {
                $birthDate = new DateYear();
                $birthDate->setValue($year);
                $person->setBirthDate($birthDate);
            }
            $place->addPersonBorn($person);
        }
        $people = $place->getPeopleBorn();
        $this->assertCount(6, $people);
        $this->assertSame('d', $people[0]->getFullName());
        $this->assertSame('f', $people[1]->getFullName());
        $this->assertSame('b', $people[2]->getFullName());
        $this->assertSame('a', $people[3]->getFullName());
        $this->assertSame('e', $people[4]->getFullName());
        $this->assertSame('c', $people[5]->getFullName());
    }

    public function testGetPeopleDied() : void {
        $place = new Place();
        foreach (['a' => 1950, 'b' => 1930, 'c' => 1970, 'd' => null, 'e' => 1960, 'f' => null] as $name => $year) {
            $person = new Person();
            $person->setFullName($name);
            if (null !== $year) {
                $deathDate = new DateYear();
                $deathDate->setValue($year);
                $person->setBirthDate($deathDate);
            }
            $place->addPersonDied($person);
        }
        $people = $place->getPeopleDied();
        $this->assertCount(6, $people);
        $this->assertSame('d', $people[0]->getFullName());
        $this->assertSame('f', $people[1]->getFullName());
        $this->assertSame('b', $people[2]->getFullName());
        $this->assertSame('a', $people[3]->getFullName());
        $this->assertSame('e', $people[4]->getFullName());
        $this->assertSame('c', $people[5]->getFullName());
    }

    public function testAddResident() : void {
        $place = new Place();
        $person = new Person();

        $place->addResident($person);
        $place->addResident($person);
        $this->assertCount(1, $place->getResidents());
    }

    public function testGetResidents() : void {
        $place = new Place();
        foreach (['b', 'd', 'a', 'c'] as $name) {
            $person = new Person();
            $person->setFullName($name);
            $person->setSortableName($name);
            $place->addResident($person);
        }
        $people = $place->getResidents();
        $this->assertCount(4, $people);
        $this->assertSame('a', $people[0]->getFullName());
        $this->assertSame('b', $people[1]->getFullName());
        $this->assertSame('c', $people[2]->getFullName());
        $this->assertSame('d', $people[3]->getFullName());
    }

    public function testAddPublication() : void {
        $place = new Place();
        $publication = new Book();

        $place->addPublication($publication);
        $place->addPublication($publication);
        $this->assertCount(1, $place->getPublications());
    }

    public function testGetPublications() : void {
        $place = new Place();
        foreach (['b', 'd', 'a', 'c'] as $name) {
            $publication = new Periodical();
            $publication->setTitle($name);
            $publication->setSortableTitle($name);
            $place->addPublication($publication);
        }
        $publications = $place->getPublications();
        $this->assertCount(4, $publications);
        $this->assertSame('a', $publications[0]->getTitle());
        $this->assertSame('b', $publications[1]->getTitle());
        $this->assertSame('c', $publications[2]->getTitle());
        $this->assertSame('d', $publications[3]->getTitle());
    }
}
