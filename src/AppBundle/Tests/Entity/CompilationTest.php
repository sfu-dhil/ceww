<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Compilation;
use AppBundle\Entity\Publication;
use PHPUnit_Framework_TestCase;

class CompilationTest extends PHPUnit_Framework_TestCase {

    public function testGetCategory() {
        $compilation = new Compilation();        
        $this->assertEquals(Publication::COMPILATION, $compilation->getCategory());
    }

}    