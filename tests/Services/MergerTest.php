<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Place;
use AppBundle\Entity\Periodical;
use AppBundle\Repository\PlaceRepository;
use AppBundle\Services\Merger;
use AppBundle\DataFixtures\ORM\LoadBook;
use AppBundle\DataFixtures\ORM\LoadCompilation;
use AppBundle\DataFixtures\ORM\LoadPeriodical;
use AppBundle\DataFixtures\ORM\LoadPerson;
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

    public function setUp() : void {
        parent::setUp();
        $this->merger = $this->getContainer()->get(Merger::class);
    }

    protected function getFixtures() {
        return array(
            LoadPerson::class,
            LoadPeriodical::class,
            LoadBook::class,
            LoadCompilation::class,
        );
    }

    public function testPlaceMerge() {
        $this->merger->places($this->getReference('place.3'), [
            $this->getReference('place.2'),
            $this->getReference('place.1'),
        ]);

        $repo = $this->em->getRepository(Place::class);
        $mergedPlaces = $repo->findAll();
        $this->assertEquals(1, count($mergedPlaces));

        $place = $this->getReference('place.3');
        $this->assertEquals(1, count($place->getPeopleBorn()));
        $this->assertEquals(1, count($place->getPeopleDied()));
        $this->assertEquals(0, count($place->getResidents()));
        $this->assertEquals(4, count($place->getPublications()));
    }

    public function testPeriodicalMerge() {
        $repo = $this->em->getRepository(Periodical::class);
        $this->merger->periodicals($this->getReference('periodical.1'), [
            $this->getReference('periodical.2'),
        ]);
        $repo->clear();
        $periodicals = $repo->findAll();
        $this->assertCount(1, $periodicals);
        $this->assertCount(2, $periodicals[0]->getGenres());
        $this->assertEquals("note 1\n\nnote 2", $periodicals[0]->getNotes());
        $this->assertCount(2, $periodicals[0]->getContributions());
        $this->assertCount(2, $periodicals[0]->getPublishers());
    }

}
