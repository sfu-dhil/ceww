<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class GenreFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        $genre1 = new Genre();
        $genre1->setName('fiction');
        $genre1->setLabel('Fiction');
        $this->setReference('genre.1', $genre1);
        $manager->persist($genre1);

        $genre2 = new Genre();
        $genre2->setName('non-fiction');
        $genre2->setLabel('Non fiction');
        $this->setReference('genre.2', $genre2);
        $manager->persist($genre2);

        $manager->flush();
    }
}
