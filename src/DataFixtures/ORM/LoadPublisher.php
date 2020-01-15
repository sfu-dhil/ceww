<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\DataFixtures\ORM;

use App\Entity\Publisher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class LoadPublisher extends Fixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager) {
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
        return array(
            LoadPlace::class,
        );
    }
}
