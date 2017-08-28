<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\DateYear;
use AppBundle\Entity\Person;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadPerson extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $person = new Person();
        $person->setFullName("Bobby Janesdotter");
        $person->setSortableName("janesdotter, bobby");
        $person->addAlias($this->getReference('alias.1'));
        $person->setBirthPlace($this->getReference('place.1'));
        $person->setDeathPlace($this->getReference('place.2'));
        
        $deathDate = new DateYear();
        $deathDate->setValue('C1800');
        $this->setReference('person.1.deathDate', $deathDate);
        $manager->persist($deathDate);        
        $person->setDeathDate($deathDate);
        
        $this->setReference('person.1', $person);
        $manager->persist($person);
                
        $manager->flush();
    }

    public function getDependencies() {
        return [
            LoadAlias::class,
            LoadPlace::class,
        ];
    }

}
