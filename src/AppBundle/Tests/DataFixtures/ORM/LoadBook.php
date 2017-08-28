<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Tests\DataFixtures\ORM;

use AppBundle\Entity\Book;
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
class LoadBook extends AbstractFixture implements DependentFixtureInterface {

    public function load(ObjectManager $manager) {
        $book = new Book();
        $book->setTitle("A Book Title");
        $book->setSortableTitle("book title, a");
        $book->addGenre($this->getReference("genre.1"));
        $book->setLocation($this->getReference("place.1"));
        
        $contribution = new Contribution();
        $contribution->setPerson($this->getReference("person.1"));
        $contribution->setRole($this->getReference("role.1"));
        $contribution->setPublication($book);
        $manager->persist($contribution);
        $this->setReference('book.1.contribution.1', $contribution);
        $book->addContribution($contribution);        
        
        $dateYear = new DateYear();
        $dateYear->setValue('1901');
        $manager->persist($dateYear);
        $this->setReference('book.1.dateyear', $dateYear);
        $book->setDateYear($dateYear);
        
        $this->setReference('book.1', $book);
        $manager->persist($book);
                
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
