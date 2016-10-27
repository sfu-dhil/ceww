<?php

/*
    * To change this license header, choose License Headers in Project Properties.
    * To change this template file, choose Tools | Templates
    * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM\dev;

use AppBundle\Entity\Publication;
use AppBundle\Tests\Utilities\AbstractDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Description of LoadPublications
 *
 * @author mjoyce
 */
class LoadPublications extends AbstractDataFixture implements OrderedFixtureInterface
{

    protected function doLoad(ObjectManager $manager) {
        $book = new Publication();
        $book->setCategory($this->getReference('category.book'));
        $book->setTitle('Things and Stuff');
        $book->setSortableTitle('things and stuff');
        $book->setYear(1980);
        $this->setReference('publication.book', $book);
        $manager->persist($book);

        $periodical = new Publication();
        $periodical->setCategory($this->getReference('category.periodical'));
        $periodical->setTitle('Things and stuff and things');
        $periodical->setSortableTitle('things and stuff');
        $periodical->setYear(1980);
        $this->setReference('publication.periodical', $periodical);
        $manager->persist($periodical);

        $anthology = new Publication();
        $anthology->setCategory($this->getReference('category.anthology'));
        $anthology->setTitle('Things and Stuff, An Anthology');
        $anthology->setSortableTitle('things and stuff');
        $anthology->setYear(1980);
        $this->setReference('publication.anthology', $anthology);
        $manager->persist($anthology);
        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }

    protected function getEnvironments() {
        return ['dev'];
    }

}
