<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Compilation;
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
class LoadCompilation extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $compilation = new Compilation();
        $compilation->setTitle("A Compilation Title");
        $compilation->setSortableTitle("compilation title, a");
        $compilation->addGenre($this->getReference("genre.1"));
        $compilation->setLocation($this->getReference("place.1"));
        
        $contribution = new Contribution();
        $contribution->setPerson($this->getReference("person.1"));
        $contribution->setRole($this->getReference("role.1"));
        $contribution->setPublication($compilation);
        $manager->persist($contribution);
        $this->setReference('compilation.1.contribution.1', $contribution);
        $compilation->addContribution($contribution);        
        
        $dateYear = new DateYear();
        $dateYear->setValue('1901');
        $manager->persist($dateYear);
        $this->setReference('compilation.1.dateyear', $dateYear);
        $compilation->setDateYear($dateYear);
        
        $this->setReference('compilation.1', $compilation);
        $manager->persist($compilation);
                
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
