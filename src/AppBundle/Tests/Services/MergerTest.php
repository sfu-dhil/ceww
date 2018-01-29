<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Place;
use AppBundle\Repository\PlaceRepository;
use AppBundle\Services\Merger;
use AppBundle\Tests\DataFixtures\ORM\LoadBook;
use AppBundle\Tests\DataFixtures\ORM\LoadCompilation;
use AppBundle\Tests\DataFixtures\ORM\LoadPeriodical;
use AppBundle\Tests\DataFixtures\ORM\LoadPerson;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class MergerTest extends BaseTestCase {

    /**
     * @var Merger
     */
    protected $merger;
    
    /**
     * @var PlaceRepository
     */
    protected $repo;

    public function setUp() {
        parent::setUp();
        $this->merger = $this->getContainer()->get(Merger::class);
        $this->repo = $this->getContainer()->get('doctrine')->getRepository(Place::class);
    }

    protected function getFixtures() {
        return array(
            LoadPerson::class,
            LoadPeriodical::class,
            LoadBook::class,
            LoadCompilation::class,
        );
    }
        
    public function testMerge() {
        $this->merger->places($this->getReference('place.3'), [
            $this->getReference('place.2'),
            $this->getReference('place.1'),
        ]);
        
        $mergedPlaces = $this->repo->findAll();
        $this->assertEquals(1, count($mergedPlaces));
        
        $place = $this->getReference('place.3');
        $this->assertEquals(1, count($place->getPeopleBorn()));
        $this->assertEquals(1, count($place->getPeopleDied()));
        $this->assertEquals(0, count($place->getResidents()));
        $this->assertEquals(3, count($place->getPublications()));
    }


}
