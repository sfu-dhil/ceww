<?php

namespace AppBundle\Tests\Fixtures;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategories extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager) {
        
        $test = new Category();
        $test->setLabel('Test');            
        $test->setName('test');
        $this->setReference("category.test", $test);        
        $manager->persist($test);
        
        $book = new Category();
        $book->setLabel('Book');            
        $book->setName('book');
        $this->setReference("category.book", $book);        
        $manager->persist($book);

        $collection = new Category();
        $collection->setLabel('Collection');            
        $collection->setName('collection');
        $this->setReference("category.collection", $collection);        
        $manager->persist($collection);
        
        $periodical = new Category();
        $periodical->setLabel('Periodical');            
        $periodical->setName('periodical');
        $this->setReference("category.periodical", $periodical);        
        $manager->persist($periodical);
        
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}
