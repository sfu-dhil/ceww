<?php

namespace App\Tests\Services;

use App\DataFixtures\BookFixtures;
use App\DataFixtures\CompilationFixtures;
use App\DataFixtures\PeriodicalFixtures;
use App\DataFixtures\PersonFixtures;
use App\DataFixtures\PlaceFixtures;
use App\Entity\Periodical;
use App\Entity\Place;
use App\Repository\PlaceRepository;
use App\Services\Merger;
use Nines\UtilBundle\Tests\ServiceBaseCase;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class MergerTest extends ServiceBaseCase {

    /**
     * @var Merger
     */
    protected $merger;

    /**
     * @var PlaceRepository
     */
    protected $repo;

    protected function fixtures() : array {
        return array(
            PersonFixtures::class,
            PeriodicalFixtures::class,
            BookFixtures::class,
            CompilationFixtures::class,
            PlaceFixtures::class,
        );
    }

    public function testPlaceMerge() {
        $this->merger->places($this->getReference('place.3', true), array(
            $this->getReference('place.2', true),
            $this->getReference('place.1', true),
        ));

        $repo = $this->entityManager->getRepository(Place::class);
        $mergedPlaces = $repo->findAll();
        $this->assertEquals(1, count($mergedPlaces));

        $place = $this->getReference('place.3');
        $this->assertEquals(1, count($place->getPeopleBorn()));
        $this->assertEquals(1, count($place->getPeopleDied()));
        $this->assertEquals(0, count($place->getResidents()));
        $this->assertEquals(4, count($place->getPublications()));
    }

    public function testPeriodicalMerge() {
        $repo = $this->entityManager->getRepository(Periodical::class);
        $this->merger->periodicals($this->getReference('periodical.1', true), array(
            $this->getReference('periodical.2', true),
        ));
        $periodicals = $repo->findAll();
        $this->assertCount(1, $periodicals);
        $this->assertCount(2, $periodicals[0]->getGenres());
        $this->assertEquals("note 1\n\nnote 2", $periodicals[0]->getNotes());
        $this->assertCount(2, $periodicals[0]->getContributions());
        $this->assertCount(2, $periodicals[0]->getPublishers());
    }

    public function setUp() : void {
        parent::setUp();
        $this->merger = self::$container->get(Merger::class);
    }
}
