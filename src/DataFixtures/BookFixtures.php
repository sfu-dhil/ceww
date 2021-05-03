<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Contribution;
use App\Entity\DateYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class BookFixtures extends Fixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager) : void {
        $book = new Book();
        $book->setTitle('A Book Title');
        $book->setSortableTitle('book title, a');
        $book->addGenre($this->getReference('genre.1'));
        $book->setLocation($this->getReference('place.1'));

        $contribution = new Contribution();
        $contribution->setPerson($this->getReference('person.1'));
        $contribution->setRole($this->getReference('role.1'));
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
            PlaceFixtures::class,
            PersonFixtures::class,
            GenreFixtures::class,
            RoleFixtures::class,
            PublisherFixtures::class,
        ];
    }
}
