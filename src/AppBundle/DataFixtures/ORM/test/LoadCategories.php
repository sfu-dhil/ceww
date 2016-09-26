<?php

namespace AppBundle\DataFixtures\ORM\test;

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
        $manager->flush();
    }

    public function getOrder() {
        return 1;        
    }

    protected function getEnvironments() {
        return ['test'];
    }

}
