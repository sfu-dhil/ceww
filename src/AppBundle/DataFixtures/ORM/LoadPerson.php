<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\DateYear;
use AppBundle\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadPerson extends Fixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $female = new Person();
        $female->setFullName("Bobby Janesdotter");
        $female->setGender(Person::FEMALE);
        $female->setSortableName("janesdotter, bobby");
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
        $male->setFullName("Bobby Fatale");
        $male->setSortableName("fatale, bobby");
        $male->setGender(Person::MALE);
        
        $this->setReference('person.2', $male);
        $manager->persist($male);
        
        $unknown = new Person();
        $unknown->setFullName("Bobby Mysterioso");
        $unknown->setSortableName("mysterioso, bobby");
        
        $this->setReference('person.3', $unknown);
        $manager->persist($unknown);
        $manager->flush();
    }

    public function getDependencies() {
        return [
            LoadAlias::class,
            LoadPlace::class,
        ];
    }

}
