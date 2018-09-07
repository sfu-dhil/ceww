<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Contribution;
use AppBundle\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase {
    
    public function testAddContribution() {
        $role = new Role();
        $contribution = new Contribution();
        
        $role->addContribution($contribution);
        $this->assertEquals(1, count($role->getContributions()));
    }
    
    public function testRemoveContribution() {
        $role = new Role();
        $contribution = new Contribution();
        
        $role->addContribution($contribution);
        $role->removeContribution($contribution);
        
        $this->assertEquals(0, count($role->getContributions()));
    }
} 