<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Place;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadPlace extends AbstractFixture {

    public function load(ObjectManager $manager) {
        $place1 = new Place();
        $place1->setName("Lockside");
        $place1->setCountryName("UK");
        $this->setReference('place.1', $place1);
        $manager->persist($place1);       
        
        $place2 = new Place();
        $place2->setName("Lockchester");
        $place2->setCountryName("CA");
        $this->setReference('place.2', $place2);
        $manager->persist($place2);       
        
        $place3 = new Place();
        $place3->setName("Colchester");
        $place3->setCountryName("CA");
        $this->setReference('place.3', $place3);
        $manager->persist($place3);       
        
        $manager->flush();
    }

}
