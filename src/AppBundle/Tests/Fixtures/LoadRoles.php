<?php

namespace AppBundle\Tests\Fixtures;

use AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoles extends AbstractFixture implements OrderedFixtureInterface {

    public function load(ObjectManager $manager) {
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

}
