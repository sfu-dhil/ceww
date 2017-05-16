<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM\dev;

use AppBundle\Entity\Role;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadRoles extends AbstractDataFixture implements OrderedFixtureInterface {

    protected function doLoad(ObjectManager $manager) {
        $role = new Role();
        $role->setLabel('Author');
        $role->setName('author');
        $manager->persist($role);
        $this->setReference('role.author', $role);
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

    protected function getEnvironments() {
        return ['dev'];
    }

}
