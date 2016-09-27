<?php

namespace AppBundle\DataFixtures\ORM\dev;

use AppBundle\Entity\Place;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadPlaces
 *
 * @author mjoyce
 */
class LoadPlaces extends AbstractDataFixture implements OrderedFixtureInterface {

    private static $PLACES = array('Tuscon, AZ', 'Paris, FR');
    
    protected function doLoad(ObjectManager $manager) {
        foreach(self::$PLACES as $name) {
            $place = new Place();
            $place->setName($name);
            $manager->persist($place);
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
