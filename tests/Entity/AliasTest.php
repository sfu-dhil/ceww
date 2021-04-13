<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\DataFixtures\AliasFixtures;
use App\Entity\Alias;
use App\Entity\Person;
use Nines\UtilBundle\Tests\BaseCase;

class AliasTest extends BaseCase
{
    protected function fixtures() : array {
        return [
            AliasFixtures::class,
        ];
    }

    public function testAppendNote() : void {
        $alias = new Alias();
        $testNote = 'This is a note to append.';

        $alias->appendNote($testNote);
        $this->assertSame($testNote, $alias->getNotes());
    }

    public function testAddPerson() : void {
        $testPerson = new Person();

        $alias = $this->references->getReference('alias.1');

        $alias->addPerson($testPerson);
        $this->assertCount(1, $alias->getPeople());

        $alias->removePerson($testPerson);
        $this->assertEmpty($alias->getPeople());
    }
}
