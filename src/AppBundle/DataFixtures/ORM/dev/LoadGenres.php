<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM\dev;

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

    private static $GENRES = [
        [
            'label' => 'Fiction',
            'description' => 'This category includes all prose writing that is understood to be the invention of the author, regardless of length (for example, novel, short story, short story collection).'
        ], [
            'label' => 'Non-fiction',
            'description' => '',
        ], [
            'label' => 'Poetry',
            'description' => 'This category includes all items in poetic form, regardless of length.',
        ], [
            'label' => 'Drama',
            'description' => 'This category includes all titles prepared for presentation in a dramatic forum (for example, plays, dialogues, spoken monologues).',
        ], [
            'label' => 'Autobiography',
            'description' => 'This category includes all form of writing about the author\'s own life (for example, memoirs, diaries, letters, personal travel writing).',
        ], [
            'label' => 'Biography',
            'description' => 'This category includes all biographies of individuals other than the author, regardless of degree of scholarly detail (for example, popular biographies, accounts of historical personalities).',
        ], [
            'label' => 'Mixed',
            'description' => 'This category is used for published volumes containing more than one type of writing.',
        ], [
            'label' => 'Uncertain',
            'description' => 'This is the default category, used for titles for which the genre is unknown or difficult to determine.',
        ]
        
    ];

    protected function doLoad(ObjectManager $manager) {
        foreach (self::$GENRES as $g) {
            $genre = new Genre();
            $genre->setName(strtolower($g['label']));
            $genre->setLabel($g['label']);
            $genre->setDescription($g['description']);
            $manager->persist($genre);
            $lc = strtolower($g['label']);
            $this->setReference("genre.{$lc}", $genre);
        }
        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }

    protected function getEnvironments() {
        return ['dev'];
    }

// put your code here
}
