<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Contribution;
use AppBundle\Entity\Role;
use PHPUnit_Framework_TestCase;

class RoleTest extends PHPUnit_Framework_TestCase {
    
    public function testAddContribution() {
        $role = new Role();
        $contribution = new Contribution();
        
        $role->addContribution($contribution);
        $role->addContribution($contribution);
        $this->assertEquals(1, count($role->getContributions()));
    }
} 