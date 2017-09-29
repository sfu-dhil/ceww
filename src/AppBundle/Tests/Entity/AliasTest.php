<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Person;
use PHPUnit_Framework_TestCase;

class AliasTest extends PHPUnit_Framework_TestCase {
    
    public function testAppendNote(){
        
        $alias = new Alias();
        $testNote = "This is a note to append.";
        
        $alias->appendNote($testNote);
        $this->assertEquals($testNote, $alias->getNotes());
    }
    
    public function testAddPerson() {
        
        $testPerson = new Person();
        $alias = new Alias();
        
        $alias->addPerson($testPerson);
        $this->assertCount(1, $alias->getPeople());
        
        //$this->assertContains($testPerson, $alias->getPeople());
  
        $alias->removePerson($testPerson);
        $this->assertEmpty($alias->getPeople());
    }
    
    
    
}

