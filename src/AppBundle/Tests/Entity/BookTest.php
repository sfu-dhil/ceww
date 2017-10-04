<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Book;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Contribution;
use PHPUnit_Framework_TestCase;

class BookTest extends PHPUnit_Framework_TestCase {

    public function testGetCategory() {
        $book = new Book();        
        $this->assertEquals(Publication::BOOK, $book->getCategory());
    }
    
    public function testAppendNote() {
        
        $publication = new Book();
        $testNote = "This is a note to append. ";
        
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
     */ 
    public function testSetDateYear($testDate) {
        
        $publication = new Book();
        $publication->setDateYear($testDate);
        
        $this->AssertEquals($testDate, $publication->getDateYear()->getValue());
      
    }
    public function SetDateYearData() {
        
        return array(
                [1800],
                ["1800"],
                ["c1800"],
                [-1800],
            
                ["1800-"],
                ["c1800-"],
            
                ["-1800"],
                ["-c1800"],
            
                ["1800-1801"],
                ["c1800-1801"],
                ["1800-c1801"],
                ["c1800-c1801"],

        );
    }
}    