<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Periodical;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Contribution;
use PHPUnit\Framework\TestCase;

class PeriodicalTest extends TestCase {

    public function testGetCategory() {
        $compilation = new Periodical();        
        $this->assertEquals(Publication::PERIODICAL, $compilation->getCategory());
    }
    
    public function testAppendNote() {
        
        $publication = new Periodical();
        $testNote = "This is a note to append. ";
        
        $publication->appendNote($testNote);
        
        $this->assertEquals($testNote, $publication->getNotes());
    }
    
    public function testAddGenre() {
        $publication = new Periodical();
        $genre = new Genre();
        
        $publication->addGenre($genre);
        
        $this->assertEquals(1, count($publication->getGenres()));
    }
    
    public function testAddContribution() {
        $publication = new Periodical();
        $contribution = new Contribution();
        
        $publication->addContribution($contribution);
        
        
        $this->assertEquals(1, count($publication->getContributions()));
    }
    
     /**
     * @dataProvider SetDateYearData
     */ 
    public function testSetDateYear($testDate) {
        
        $publication = new Periodical();
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