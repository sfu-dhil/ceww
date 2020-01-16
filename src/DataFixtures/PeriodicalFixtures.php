<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Contribution;
use App\Entity\DateYear;
use App\Entity\Periodical;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class PeriodicalFixtures extends Fixture implements DependentFixtureInterface {
    public function load(ObjectManager $manager) : void {
        for ($i = 1; $i <= 2; $i++) {
            $periodical = new Periodical();
            $periodical->setTitle("A Periodical Title {$i}");
            $periodical->setSortableTitle("periodical title {$i}, a");
            $periodical->addGenre($this->getReference("genre.{$i}"));
            $periodical->setLocation($this->getReference("place.{$i}"));
            $periodical->setRunDates("190{$i}-");
            $periodical->addLink("http://example.com/{$i}");
            $periodical->addPublisher($this->getReference("publisher.{$i}"));
            $periodical->setNotes("note {$i}");

            $contribution = new Contribution();
            $contribution->setPerson($this->getReference("person.{$i}"));
            $contribution->setRole($this->getReference('role.1'));
            $contribution->setPublication($periodical);
            $manager->persist($contribution);
            $this->setReference("periodical.{$i}.contribution.1", $contribution);
            $periodical->addContribution($contribution);

            $dateYear = new DateYear();
            $dateYear->setValue(1900 + $i);
            $manager->persist($dateYear);
            $this->setReference("periodical.{$i}.dateyear", $dateYear);
            $periodical->setDateYear($dateYear);

            $this->setReference("periodical.{$i}", $periodical);
            $manager->persist($periodical);
        }
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
