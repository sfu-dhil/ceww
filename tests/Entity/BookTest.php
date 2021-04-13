<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Contribution;
use App\Entity\Genre;
use App\Entity\Publication;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testGetCategory() : void {
        $book = new Book();
        $this->assertSame(Publication::BOOK, $book->getCategory());
    }

    public function testAppendNote() : void {
        $publication = new Book();
        $testNote = 'This is a note to append. ';

        $publication->appendNote($testNote);

        $this->assertSame($testNote, $publication->getNotes());
    }

    public function testAddGenre() : void {
        $publication = new Book();
        $genre = new Genre();

        $publication->addGenre($genre);

        $this->assertCount(1, $publication->getGenres());
    }

    public function testAddContribution() : void {
        $publication = new Book();
        $contribution = new Contribution();

        $publication->addContribution($contribution);

        $this->assertCount(1, $publication->getContributions());
    }

    /**
     * @dataProvider SetDateYearData
     *
     * @param mixed $testDate
     */
    public function testSetDateYear($testDate) : void {
        $publication = new Book();
        $publication->setDateYear($testDate);

        $this->AssertEquals($testDate, $publication->getDateYear()->getValue());
    }

    public function SetDateYearData() {
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
}
