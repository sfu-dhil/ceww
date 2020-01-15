<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\DataFixtures\ORM;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class LoadRole extends Fixture {
    public function load(ObjectManager $manager) {
        $role = new Role();
        $role->setName('author');
        $role->setLabel('Author');
        $this->setReference('role.1', $role);
        $manager->persist($role);

        $editor = new Role();
        $editor->setName('editor');
        $editor->setLabel('Editor');
        $this->setReference('role.2', $editor);
        $manager->persist($editor);
        $manager->flush();
    }
}
