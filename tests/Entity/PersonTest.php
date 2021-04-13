<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Compilation;
use App\Entity\Contribution;
use App\Entity\Periodical;
use App\Entity\Person;
use App\Entity\Publication;
use Nines\UtilBundle\Tests\BaseCase;

class PersonTest extends BaseCase
{
    /**
     * @dataProvider SetBirthDateData
     *
     * @param mixed $testDate
     */
    public function testSetBirthDate($testDate) : void {
        $person = new Person();

        $person->setBirthDate($testDate);
        //compare string or values, not whole objects
        $this->AssertEquals($testDate, $person->getBirthDate()->getValue());
    }

    // dataProvider function name should not begin with 'test'
    public function SetBirthDateData() {
        return [
            [1800],
            ['1800'],
            ['c1800'],
            [-1800],

            ['1800-'],
            ['c1800-'],

            ['-1800'],
            ['-c1800'],

            ['1800-1801'],
            ['c1800-1801'],
            ['1800-c1801'],
            ['c1800-c1801'],
        ];
    }

    /**
     * @dataProvider SetDeathDateData
     *
     * @param mixed $testDate
     */
    public function testSetDeathDate($testDate) : void {
        $person = new Person();

        $person->setDeathDate($testDate);
        //compare string or values, not whole objects
        $this->AssertEquals($testDate, $person->getDeathDate()->getValue());
    }

    // dataProvider function name should not begin with 'test'
    public function SetDeathDateData() {
        return [
            [1800],
            ['1800'],
            ['c1800'],
            [-1800],

            ['1800-'],
            ['c1800-'],

            ['-1800'],
            ['-c1800'],

            ['1800-1801'],
            ['c1800-1801'],
            ['1800-c1801'],
            ['c1800-c1801'],
        ];
    }

    public function testGetContributions() : void {
        $person = new Person();

        foreach ([1, 2, 3] as $n) {
            $book = new Book();
            $book->setTitle("Book {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($book);
            $person->addContribution($contribution);
        }

        foreach ([4, 5] as $n) {
            $compilation = new Compilation();
            $compilation->setTitle("Compilation {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($compilation);
            $person->addContribution($contribution);
        }

        foreach ([6, 7] as $n) {
            $periodical = new Periodical();
            $periodical->setTitle("Publication {$n}");
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setPublication($periodical);
            $person->addContribution($contribution);
        }
        $this->assertCount(3, $person->getContributions(Publication::BOOK));
        $this->assertCount(2, $person->getContributions(Publication::COMPILATION));
        $this->assertCount(2, $person->getContributions(Publication::PERIODICAL));
    }

    public function testAddUrlLink() : void {
        $person = new Person();
        $urlLink = 'http://www.example.com';

        $person->addUrlLink($urlLink);

        $this->assertCount(1, $person->getUrlLinks());
    }

    public function testRemoveUrlLink() : void {
        $person = new Person();
        $urlLink = 'http://www.example.com';

        $person->addUrlLink($urlLink);
        $person->removeUrlLink($urlLink);

        $this->assertCount(0, $person->getUrlLinks());
    }

    public function testGetUrlLinks() : void {
        $person = new Person();
        $urlLinks = ['http://www.example.com', 'http://www.sfu.ca'];

        $person->setUrlLinks($urlLinks);

        $this->assertCount(2, $person->getUrlLinks());
        $this->assertContains('http://www.example.com', $person->getUrlLinks());
    }

    /**
     * @dataProvider SetUrlLinkData
     *
     * @param mixed $testUrlLinks
     */
    public function testSetUrlLinks($testUrlLinks) : void {
        $person = new Person();

        $person->setUrlLinks($testUrlLinks);

        $this->AssertEquals($testUrlLinks, $person->getUrlLinks());
    }

    public function SetUrlLinkData() {
        return [
            [['http://www.example.com', 'http://www.sfu.ca']],
            [['http://www.sfu.ca']],
        ];
    }
}
