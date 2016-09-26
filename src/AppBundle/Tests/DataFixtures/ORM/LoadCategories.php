<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadPlaces
 *
 * @author mjoyce
 */
class LoadCategories extends AbstractDataFixture implements OrderedFixtureInterface {

    protected function doLoad(ObjectManager $manager) {
        $type = new Category();
        $type->setLabel('Book');
        $manager->persist($type);
        $manager->flush($type);
        $this->setReference('c1', $type);
    }

    public function getOrder() {
        return 1;        
    }

    protected function getEnvironments() {
        return ['test'];
    }

}
