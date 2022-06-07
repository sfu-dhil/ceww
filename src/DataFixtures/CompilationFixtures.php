<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Compilation;
use App\Entity\Contribution;
use App\Entity\DateYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Description of LoadGenres.
 *
 * @author mjoyce
 */
class CompilationFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    public function load(ObjectManager $manager) : void {
        $compilation = new Compilation();
        $compilation->setTitle('A Compilation Title');
        $compilation->setSortableTitle('compilation title, a');
        $compilation->addGenre($this->getReference('genre.1'));
        $compilation->setLocation($this->getReference('place.1'));

        $contribution = new Contribution();
        $contribution->setPerson($this->getReference('person.1'));
        $contribution->setRole($this->getReference('role.1'));
        $contribution->setPublication($compilation);
        $manager->persist($contribution);
        $this->setReference('compilation.1.contribution.1', $contribution);
        $compilation->addContribution($contribution);

        $dateYear = new DateYear();
        $dateYear->setValue('1901');
        $manager->persist($dateYear);
        $this->setReference('compilation.1.dateyear', $dateYear);
        $compilation->setDateYear($dateYear);

        $this->setReference('compilation.1', $compilation);
        $manager->persist($compilation);

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
