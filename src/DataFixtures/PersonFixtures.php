<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\DateYear;
use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class PersonFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        $female = new Person();
        $female->setFullName('Bobby Janesdotter');
        $female->setGender(Person::FEMALE);
        $female->setSortableName('janesdotter, bobby');
        $female->addAlias($this->getReference('alias.1'));
        $female->setBirthPlace($this->getReference('place.1'));
        $female->setDeathPlace($this->getReference('place.2'));

        $deathDate = new DateYear();
        $deathDate->setValue('C1800');
        $this->setReference('person.1.deathDate', $deathDate);
        $manager->persist($deathDate);
        $female->setDeathDate($deathDate);

        $this->setReference('person.1', $female);
        $manager->persist($female);

        $male = new Person();
        $male->setFullName('Bobby Fatale');
        $male->setSortableName('fatale, bobby');
        $male->setGender(Person::MALE);

        $this->setReference('person.2', $male);
        $manager->persist($male);

        $unknown = new Person();
        $unknown->setFullName('Bobby Mysterioso');
        $unknown->setSortableName('mysterioso, bobby');

        $this->setReference('person.3', $unknown);
        $manager->persist($unknown);
        $manager->flush();
    }

    public function getDependencies() {
        return [
            AliasFixtures::class,
            PlaceFixtures::class,
        ];
    }
}
