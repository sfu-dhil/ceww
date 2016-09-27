<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM\dev;

use AppBundle\Entity\Alias;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadAliases
 *
 * @author mjoyce
 */
class LoadAliases  extends AbstractDataFixture implements OrderedFixtureInterface {
    
    private static $ALIASES = array('Alice', 'Gertrude');
    
    protected function doLoad(ObjectManager $manager) {
        foreach(self::$ALIASES as $name) {
            $alias = new Alias();
            $alias->setName($name);
            $manager->persist($alias);
            $lc = strtolower($name);
            $this->setReference("alias.{$lc}", $alias);
        }
        $manager->flush();
    }

    protected function getEnvironments() {
        return ['dev'];
    }
}
