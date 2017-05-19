<?php

namespace AppBundle\DataFixtures\ORM\dev;

use AppBundle\Entity\Category;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadPlaces
 *
 * @author mjoyce
 */
class LoadCategories extends AbstractDataFixture implements OrderedFixtureInterface
{

    private static $CATEGORIES = array('Book', 'Periodical', 'Collection');

    protected function doLoad(ObjectManager $manager) {
        foreach (self::$CATEGORIES as $label) {
            $category = new Category();
            $category->setLabel($label);            
            $category->setName(strtolower($label));
            $manager->persist($category);
            $lc = strtolower($label);
            $this->setReference("category.{$lc}", $category);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

    protected function getEnvironments() {
        return ['dev'];
    }

}
