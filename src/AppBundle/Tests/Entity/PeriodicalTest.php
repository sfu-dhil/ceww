<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Periodical;
use AppBundle\Entity\Publication;
use PHPUnit_Framework_TestCase;

class PeriodicalTest extends PHPUnit_Framework_TestCase {

    public function testGetCategory() {
        $compilation = new Periodical();        
        $this->assertEquals(Publication::PERIODICAL, $compilation->getCategory());
    }

}    