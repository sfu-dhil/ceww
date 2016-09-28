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
class LoadPlaces extends AbstractDataFixture implements OrderedFixtureInterface
{

    private static $PLACES = array('Tuscon', 'Paris');
    
    protected function doLoad(ObjectManager $manager)
    {
        foreach (self::$PLACES as $name) {
            $place = new Place();
            $place->setName($name);
            $manager->persist($place);
            $lc = strtolower($name);
            $this->setReference("place.{$lc}", $place);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    protected function getEnvironments()
    {
        return ['dev'];
    }
}
