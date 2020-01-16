<?php

namespace App\Tests\Entity;

use App\DataFixtures\PeriodicalFixtures;
use App\DataFixtures\PersonFixtures;
use App\DataFixtures\RoleFixtures;
use App\Entity\Contribution;
use Nines\UtilBundle\Tests\BaseCase;

class ContributionTest extends BaseCase {
    protected function fixtures() : array {
        return array(
            PeriodicalFixtures::class,
            PersonFixtures::class,
            RoleFixtures::class,
        );
    }

    public function testGetPublicationId() {
        $contribution = new Contribution();
        $contribution->setPerson($this->getReference('person.1'));
        $contribution->setRole($this->getReference('role.1'));
        $contribution->setPublication($this->getReference('periodical.1'));

        $this->assertEquals($this->getReference('periodical.1')->getId(), $contribution->getPublicationId());
    }
}
