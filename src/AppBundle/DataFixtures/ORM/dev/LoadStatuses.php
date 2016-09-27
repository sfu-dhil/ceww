<?php

namespace AppBundle\DataFixtures\ORM\dev;

use AppBundle\Entity\Status;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadPlaces
 *
 * @author mjoyce
 */
class LoadStatuses extends AbstractDataFixture implements OrderedFixtureInterface {

    private static $STATUSES = array('Draft', 'Needs Review', 'Published');
    
    protected function doLoad(ObjectManager $manager) {
        foreach(self::$STATUSES as $label) {
            $status = new Status();
            $status->setLabel($label);
            $manager->persist($status);
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
