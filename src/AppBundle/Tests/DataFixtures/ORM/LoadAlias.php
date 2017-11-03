<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Alias;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadAlias extends AbstractFixture {

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
