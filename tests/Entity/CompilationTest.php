<?php

namespace App\Tests\Entity;

use App\Entity\Compilation;
use App\Entity\Contribution;
use App\Entity\Genre;
use App\Entity\Publication;
use PHPUnit\Framework\TestCase;

class CompilationTest extends TestCase {
    public function testGetCategory() {
        $compilation = new Compilation();
        $this->assertEquals(Publication::COMPILATION, $compilation->getCategory());
    }

    public function testAppendNote() {
        $publication = new Compilation();
        $testNote = 'This is a note to append. ';

        $publication->appendNote($testNote);

        $this->assertEquals($testNote, $publication->getNotes());
    }

    public function testAddGenre() {
        $publication = new Compilation();
        $genre = new Genre();

        $publication->addGenre($genre);

        $this->assertEquals(1, count($publication->getGenres()));
    }

    public function testAddContribution() {
        $publication = new Compilation();
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
        $publication = new Compilation();
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
