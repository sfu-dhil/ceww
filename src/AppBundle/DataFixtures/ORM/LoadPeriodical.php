<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Periodical;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\DateYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadPeriodical extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        for ($i = 1; $i <= 2; $i++) {
            $periodical = new Periodical();
            $periodical->setTitle("A Periodical Title {$i}");
            $periodical->setSortableTitle("periodical title {$i}, a");
            $periodical->addGenre($this->getReference("genre.{$i}"));
            $periodical->setLocation($this->getReference("place.{$i}"));
            $periodical->setRunDates("190{$i}-");
            $periodical->addLink("http://example.com/{$i}");
            $periodical->addPublisher($this->getReference("publisher.{$i}"));
            $periodical->setNotes("note {$i}");

            $contribution = new Contribution();
            $contribution->setPerson($this->getReference("person.{$i}"));
            $contribution->setRole($this->getReference("role.1"));
            $contribution->setPublication($periodical);
            $manager->persist($contribution);
            $this->setReference("periodical.{$i}.contribution.1", $contribution);
            $periodical->addContribution($contribution);

            $dateYear = new DateYear();
            $dateYear->setValue(1900 + $i);
            $manager->persist($dateYear);
            $this->setReference("periodical.{$i}.dateyear", $dateYear);
            $periodical->setDateYear($dateYear);

            $this->setReference("periodical.{$i}", $periodical);
            $manager->persist($periodical);
        }
        $manager->flush();
    }

    public function getDependencies() {
        return [
            LoadPlace::class,
            LoadPerson::class,
            LoadGenre::class,
            LoadRole::class,
            LoadPublisher::class,
        ];
    }

}
