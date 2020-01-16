<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\DataFixtures;

use App\Entity\Alias;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class AliasFixtures extends Fixture {
    public function load(ObjectManager $manager) {
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
