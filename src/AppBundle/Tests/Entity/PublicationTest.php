<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Book;
use AppBundle\Entity\Compilation;
use AppBundle\Entity\Periodical;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\Genre;
use PHPUnit_Framework_TestCase;

class PublicationTest extends PHPUnit_Framework_TestCase {
    
    public function testAddGenre() {
        $publication = new Book();
        $genre = new Genre();
        $publication->addGenre($genre);
        //$publication->addGenre($genre);
        $this->assertEquals(1, count($publication->getGenres()));
    }
    
    public function testAddContribution() {
        $publication = new Compilation();
        $genre = new Contribution();
        $publication->addContribution($genre);
        //$publication->addContribution($genre);
        $this->assertEquals(1, count($publication->getContributions()));
    }
    public function testAppendNote(){
        
        $testNote = "This is a note to append.";
        
        $publication1 = new Book();
        $publication2 = new Compilation();
        $publication3 = new Periodical();
        
        $publication1->appendNote($testNote);
        $publication2->appendNote($testNote);
        $publication3->appendNote($testNote);
        
        $this->assertEquals($testNote, $publication1->getNotes());
        $this->assertEquals($testNote, $publication2->getNotes());
        $this->assertEquals($testNote, $publication3->getNotes());
    }
    
     /**
     * @dataProvider SetDateYearData
     */ 
    public function testSetDateYear($testDate) {
        
        $publication1 = new Book();
        $publication1->setDateYear($testDate);
        
        $publication2 = new Compilation();
        $publication2->setDateYear($testDate);
        
        $publication3 = new Periodical();
        $publication3->setDateYear($testDate);
        
        $this->AssertEquals($testDate, $publication1->getDateYear()->getValue());
        $this->AssertEquals($testDate, $publication2->getDateYear()->getValue());
        $this->AssertEquals($testDate, $publication3->getDateYear()->getValue());
      
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
 