<?php

namespace App\Tests\Entity;

use App\DataFixtures\AliasFixtures;
use App\Entity\Alias;
use App\Entity\Person;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class AliasTest extends BaseTestCase {
    protected function fixtures() : array {
        return array(
            AliasFixtures::class,
        );
    }

    public function testAppendNote() {
        $alias = new Alias();
        $testNote = 'This is a note to append.';

        $alias->appendNote($testNote);
        $this->assertEquals($testNote, $alias->getNotes());
    }

    public function testAddPerson() {
        $testPerson = new Person();

        $alias = $this->references->getReference('alias.1');

        $alias->addPerson($testPerson);
        $this->assertCount(1, $alias->getPeople());

        $alias->removePerson($testPerson);
        $this->assertEmpty($alias->getPeople());
    }
}
