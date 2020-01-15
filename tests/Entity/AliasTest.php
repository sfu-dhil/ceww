<?php

namespace AppBundle\Tests\Entity;

use AppBundle\DataFixtures\ORM\LoadAlias;
use AppBundle\Entity\Alias;
use AppBundle\Entity\Person;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class AliasTest extends BaseTestCase {
    protected function getFixtures() {
        return array(
            LoadAlias::class,
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
