<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM\test;

use AppBundle\Entity\Genre;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadGenres
 *
 * @author mjoyce
 */
class LoadGenres extends AbstractDataFixture implements OrderedFixtureInterface {
    
    private static $GENRES = array('Fiction', 'Non-fiction');
    
    protected function doLoad(ObjectManager $manager) {
        foreach(self::$GENRES as $name) {
            $genre = new Genre();
            $genre->setName($name);
            $manager->persist($genre);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;        
    }

    protected function getEnvironments() {
        return ['test'];
    }

//put your code here
}
