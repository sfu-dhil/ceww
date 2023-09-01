<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Publisher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class PublisherFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        $publisher1 = new Publisher();
        $publisher1->setName('Cueue Stuff');
        $publisher1->addPlace($this->getReference('place.1'));

        $this->setReference('publisher.1', $publisher1);
        $manager->persist($publisher1);

        $publisher2 = new Publisher();
        $publisher2->setName('Bookery');
        $publisher2->addPlace($this->getReference('place.2'));
        $publisher2->addPlace($this->getReference('place.3'));

        $this->setReference('publisher.2', $publisher2);
        $manager->persist($publisher2);

        $manager->flush();
    }

    public function getDependencies() {
        return [
            PlaceFixtures::class,
        ];
    }
}
