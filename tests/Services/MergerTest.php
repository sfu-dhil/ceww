<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\DataFixtures\BookFixtures;
use App\DataFixtures\CompilationFixtures;
use App\DataFixtures\PeriodicalFixtures;
use App\DataFixtures\PersonFixtures;
use App\DataFixtures\PlaceFixtures;
use App\DataFixtures\PublisherFixtures;
use App\Entity\Periodical;
use App\Entity\Place;
use App\Entity\Publisher;
use App\Repository\PlaceRepository;
use App\Services\Merger;
use Nines\UtilBundle\TestCase\ServiceTestCase;

class MergerTest extends ServiceTestCase {
    /**
     * @var Merger
     */
    protected $merger;

    /**
     * @var PlaceRepository
     */
    protected $repo;

    protected function fixtures() : array {
        return [
            PersonFixtures::class,
            PeriodicalFixtures::class,
            BookFixtures::class,
            CompilationFixtures::class,
            PlaceFixtures::class,
            PublisherFixtures::class,
        ];
    }

    public function testPlaceMerge() : void {
        $this->merger->places($this->em->find(Place::class, 3), [
            $this->em->find(Place::class, 2),
            $this->em->find(Place::class, 1),
        ]);

        $repo = $this->em->getRepository(Place::class);
        $mergedPlaces = $repo->findAll();
        $this->assertCount(1, $mergedPlaces);

        $place = $this->em->find(Place::class, 3);
        $this->assertCount(1, $place->getPeopleBorn());
        $this->assertCount(1, $place->getPeopleDied());
        $this->assertCount(0, $place->getResidents());
        $this->assertCount(4, $place->getPublications());
    }

    public function testPeriodicalMerge() : void {
        $repo = $this->em->getRepository(Periodical::class);
        $this->merger->periodicals($this->em->find(Periodical::class, 2), [
            $this->em->find(Periodical::class, 3),
        ]);
        $periodicals = $repo->findAll();
        $this->assertCount(1, $periodicals);
        $this->assertCount(2, $periodicals[0]->getGenres());
        $this->assertSame("note 1\n\nnote 2", $periodicals[0]->getNotes());
        $this->assertCount(2, $periodicals[0]->getContributions());
        $this->assertCount(2, $periodicals[0]->getPublishers());
    }

    public function testPublisherMerge() : void {
        $repo = $this->em->getRepository(Publisher::class);
        $this->merger->publishers($this->em->find(Publisher::class, 1), [
            $this->em->find(Publisher::class, 2),
        ]);
        $publishers = $repo->findAll();
        $this->assertCount(1, $publishers);
        $this->assertCount(3, $publishers[0]->getPlaces());
        $this->assertCount(2, $publishers[0]->getPublications());
    }

    public function setUp() : void {
        parent::setUp();
        $this->merger = self::getContainer()->get(Merger::class);
    }
}
