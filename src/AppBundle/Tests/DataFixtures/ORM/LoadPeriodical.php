<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Periodical;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\DateYear;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadPeriodical extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $periodical = new Periodical();
        $periodical->setTitle("A Periodical Title");
        $periodical->setSortableTitle("periodical title, a");
        $periodical->addGenre($this->getReference("genre.1"));
        $periodical->setLocation($this->getReference("place.1"));
        
        $contribution = new Contribution();
        $contribution->setPerson($this->getReference("person.1"));
        $contribution->setRole($this->getReference("role.1"));
        $contribution->setPublication($periodical);
        $manager->persist($contribution);
        $this->setReference('periodical.1.contribution.1', $contribution);
        $periodical->addContribution($contribution);        
        
        $dateYear = new DateYear();
        $dateYear->setValue('1901');
        $manager->persist($dateYear);
        $this->setReference('periodical.1.dateyear', $dateYear);
        $periodical->setDateYear($dateYear);
        
        $this->setReference('periodical.1', $periodical);
        $manager->persist($periodical);
                
        $manager->flush();
    }

    public function getDependencies() {
        return [
            LoadPlace::class,
            LoadPerson::class,
            LoadGenre::class,
            LoadRole::class,
        ];
    }

}
