<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadRole extends AbstractFixture {

    public function load(ObjectManager $manager) {
        $role = new Role();
        $role->setName("author");
        $role->setLabel("Author");
        $this->setReference('role.1', $role);
        $manager->persist($role);       
        
        $manager->flush();
    }

}
