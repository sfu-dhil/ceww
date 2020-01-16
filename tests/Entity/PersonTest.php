<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Compilation;
use App\Entity\Contribution;
use App\Entity\Periodical;
use App\Entity\Person;
use App\Entity\Publication;
use Nines\UtilBundle\Tests\BaseCase;

class PersonTest extends BaseCase {
    /**
     * @dataProvider SetBirthDateData
     *
     * @param mixed $testDate
     */
    public function testSetBirthDate($testDate) {
        $person = new Person();

        $person->setBirthDate($testDate);
        //compare string or values, not whole objects
        $this->AssertEquals($testDate, $person->getBirthDate()->getValue());
    }

    // dataProvider function name should not begin with 'test'
    public function SetBirthDateData() {
        return array(
            array(1800),
            array('1800'),
            array('c1800'),
            array(-1800),

            array('1800-'),
            array('c1800-'),

            array('-1800'),
            array('-c1800'),

            array('1800-1801'),
            array('c1800-1801'),
            array('1800-c1801'),
            array('c1800-c1801'),
        );
    }

    /**
     * @dataProvider SetDeathDateData
     *
     * @param mixed $testDate
     */
    public function testSetDeathDate($testDate) {
        $person = new Person();

        $person->setDeathDate($testDate);
        //compare string or values, not whole objects
        $this->AssertEquals($testDate, $person->getDeathDate()->getValue());
    }

    // dataProvider function name should not begin with 'test'
    public function SetDeathDateData() {
        return array(
            array(1800),
            array('1800'),
            array('c1800'),
            array(-1800),

            array('1800-'),
            array('c1800-'),

            array('-1800'),
            array('-c1800'),

            array('1800-1801'),
            array('c1800-1801'),
            array('1800-c1801'),
            array('c1800-c1801'),
        );
    }

    public function testGetContributions() {
        $person = new Person();

        foreach (array(1, 2, 3) as $n) {
            $book = new Book();
            $book->setTitle("Book {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($book);
            $person->addContribution($contribution);
        }
        foreach (array(4, 5) as $n) {
            $compilation = new Compilation();
            $compilation->setTitle("Compilation {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($compilation);
            $person->addContribution($contribution);
        }

        foreach (array(6, 7) as $n) {
            $periodical = new Periodical();
            $periodical->setTitle("Publication {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($periodical);
            $person->addContribution($contribution);
        }
        $this->assertEquals(3, count($person->getContributions(Publication::BOOK)));
        $this->assertEquals(2, count($person->getContributions(Publication::COMPILATION)));
        $this->assertEquals(2, count($person->getContributions(Publication::PERIODICAL)));
    }

    public function testAddUrlLink() {
        $person = new Person();
        $urlLink = 'http://www.example.com';

        $person->addUrlLink($urlLink);

        $this->assertEquals(1, count($person->getUrlLinks()));
    }

    public function testRemoveUrlLink() {
        $person = new Person();
        $urlLink = 'http://www.example.com';

        $person->addUrlLink($urlLink);
        $person->removeUrlLink($urlLink);

        $this->assertEquals(0, count($person->getUrlLinks()));
    }

    public function testGetUrlLinks() {
        $person = new Person();
        $urlLinks = array('http://www.example.com', 'http://www.sfu.ca');

        $person->setUrlLinks($urlLinks);

        $this->assertEquals(2, count($person->getUrlLinks()));
        $this->assertContains('http://www.example.com', $person->getUrlLinks());
    }

    /**
     * @dataProvider SetUrlLinkData
     *
     * @param mixed $testUrlLinks
     */
    public function testSetUrlLinks($testUrlLinks) {
        $person = new Person();

        $person->setUrlLinks($testUrlLinks);

        $this->AssertEquals($testUrlLinks, $person->getUrlLinks());
    }

    public function SetUrlLinkData() {
        return array(
            array(array('http://www.example.com', 'http://www.sfu.ca')),
            array(array('http://www.sfu.ca')),
        );
    }
}
