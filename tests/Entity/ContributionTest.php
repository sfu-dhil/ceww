<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\DataFixtures\PeriodicalFixtures;
use App\DataFixtures\PersonFixtures;
use App\DataFixtures\RoleFixtures;
use App\Entity\Contribution;
use Nines\UtilBundle\Tests\BaseCase;

class ContributionTest extends BaseCase {
    protected function fixtures() : array {
        return [
            PeriodicalFixtures::class,
            PersonFixtures::class,
            RoleFixtures::class,
        ];
    }

    public function testGetPublicationId() : void {
        $contribution = new Contribution();
        $contribution->setPerson($this->getReference('person.1'));
        $contribution->setRole($this->getReference('role.1'));
        $contribution->setPublication($this->getReference('periodical.1'));

        $this->assertSame($this->getReference('periodical.1')->getId(), $contribution->getPublicationId());
    }
}
