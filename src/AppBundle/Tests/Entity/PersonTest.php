<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Book;
use AppBundle\Entity\Compilation;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Person;
use AppBundle\Entity\Publication;
use AppBundle\Tests\Util\BaseTestCase;

class PersonTest extends BaseTestCase {

    public function testGetContributions() {
        $person = new Person();
        foreach(array(1,2,3) as $n) {
            $book = new Book();
            $book->setTitle("Book {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($book);
            $person->addContribution($contribution);
        }
        foreach(array(4,5) as $n) {
            $compilation = new Compilation();
            $compilation->setTitle("Compilation {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($compilation);
            $person->addContribution($contribution);
        }
        $this->assertEquals(3, count($person->getContributions(Publication::BOOK)));
        $this->assertEquals(2, count($person->getContributions(Publication::COMPILATION)));
    }

}    