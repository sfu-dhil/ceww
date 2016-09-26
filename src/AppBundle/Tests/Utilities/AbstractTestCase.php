<?php

namespace AppBundle\Tests\Utilities;

use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Description of AbstractTestCase
 *
 * @author mjoyce
 */
class AbstractTestCase extends WebTestCase {
    
    public function setUp() {
        parent::setUp();
        $this->loadFixtures(array());
    }
    
}
