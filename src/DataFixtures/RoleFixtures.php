<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class RoleFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
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
