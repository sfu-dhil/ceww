<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

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
use Nines\UtilBundle\Tests\ServiceBaseCase;

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
        $this->merger->places($this->getReference('place.3', true), [
            $this->getReference('place.2', true),
            $this->getReference('place.1', true),
        ]);

        $repo = $this->entityManager->getRepository(Place::class);
        $mergedPlaces = $repo->findAll();
        $this->assertCount(1, $mergedPlaces);

        $place = $this->getReference('place.3');
        $this->assertCount(1, $place->getPeopleBorn());
        $this->assertCount(1, $place->getPeopleDied());
        $this->assertCount(0, $place->getResidents());
        $this->assertCount(4, $place->getPublications());
    }

    public function testPeriodicalMerge() : void {
        $repo = $this->entityManager->getRepository(Periodical::class);
        $this->merger->periodicals($this->getReference('periodical.1', true), [
            $this->getReference('periodical.2', true),
        ]);
        $periodicals = $repo->findAll();
        $this->assertCount(1, $periodicals);
        $this->assertCount(2, $periodicals[0]->getGenres());
        $this->assertSame("note 1\n\nnote 2", $periodicals[0]->getNotes());
        $this->assertCount(2, $periodicals[0]->getContributions());
        $this->assertCount(2, $periodicals[0]->getPublishers());
    }

    public function testPublisherMerge() : void {
        $repo = $this->entityManager->getRepository(Publisher::class);
        $this->merger->publishers($this->getReference('publisher.1', true), [
            $this->getReference('publisher.2', true),
        ]);
        $publishers = $repo->findAll();
        $this->assertCount(1, $publishers);
        $this->assertCount(3, $publishers[0]->getPlaces());
        $this->assertCount(2, $publishers[0]->getPublications());
    }

    public function setUp() : void {
        parent::setUp();
        $this->merger = self::$container->get(Merger::class);
    }
}
