<?php

/*
    * To change this license header, choose License Headers in Project Properties.
    * To change this template file, choose Tools | Templates
    * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM\test;

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
        $publication = new Publication();
        $publication->setCategory($this->getReference('category.book'));
        $publication->setTitle('Things and Stuff');
        $publication->setSortableTitle('things and stuff');
        $publication->setYear(1980);
        $this->setReference('publication.things', $publication);
        $manager->persist($publication);
        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }

    protected function getEnvironments() {
        return ['test'];
    }

}
