<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Place;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class PlaceFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        $place1 = new Place();
        $place1->setName('Lockside');
        $place1->setSortableName('lockside, uk');
        $place1->setCountryName('UK');
        $manager->persist($place1);
        $this->setReference('place.1', $place1);

        $place2 = new Place();
        $place2->setName('Lockchester');
        $place2->setSortableName('lockchester, ca');
        $place2->setCountryName('CA');
        $manager->persist($place2);
        $this->setReference('place.2', $place2);

        $place3 = new Place();
        $place3->setName('Colchester');
        $place3->setSortableName('colchester, ca');
        $place3->setCountryName('CA');
        $manager->persist($place3);
        $this->setReference('place.3', $place3);

        $manager->flush();
    }
}
