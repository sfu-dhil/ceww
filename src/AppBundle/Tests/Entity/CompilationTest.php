<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Compilation;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Contribution;
use PHPUnit\Framework\TestCase;

class CompilationTest extends TestCase {

    public function testGetCategory() {
        $compilation = new Compilation();        
        $this->assertEquals(Publication::COMPILATION, $compilation->getCategory());
    }
    
    public function testAppendNote() {
        
        $publication = new Compilation();
        $testNote = "This is a note to append. ";
     
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
     */ 
    public function testSetDateYear($testDate) {
        
        $publication = new Compilation();
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