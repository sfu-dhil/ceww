<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Contribution;
use App\Entity\Genre;
use App\Entity\Publication;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase {
    public function testGetCategory() {
        $book = new Book();
        $this->assertEquals(Publication::BOOK, $book->getCategory());
    }

    public function testAppendNote() {
        $publication = new Book();
        $testNote = 'This is a note to append. ';

        $publication->appendNote($testNote);

        $this->assertEquals($testNote, $publication->getNotes());
    }

    public function testAddGenre() {
        $publication = new Book();
        $genre = new Genre();

        $publication->addGenre($genre);

        $this->assertEquals(1, count($publication->getGenres()));
    }

    public function testAddContribution() {
        $publication = new Book();
        $contribution = new Contribution();

        $publication->addContribution($contribution);

        $this->assertEquals(1, count($publication->getContributions()));
    }

    /**
     * @dataProvider SetDateYearData
     *
     * @param mixed $testDate
     */
    public function testSetDateYear($testDate) {
        $publication = new Book();
        $publication->setDateYear($testDate);

        $this->AssertEquals($testDate, $publication->getDateYear()->getValue());
    }

    public function SetDateYearData() {
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
}
