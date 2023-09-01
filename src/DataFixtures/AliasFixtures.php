<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Alias;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class AliasFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        $alias = new Alias();
        $alias->setDescription('An alias');
        $alias->setMaiden(true);
        $alias->setName('Nee Mariston');
        $alias->setSortableName('mariston, n');
        $this->setReference('alias.1', $alias);
        $manager->persist($alias);

        $manager->flush();
    }
}
