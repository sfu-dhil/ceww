<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Contribution;
use AppBundle\DataFixtures\ORM\LoadPeriodical;
use AppBundle\DataFixtures\ORM\LoadPerson;
use AppBundle\DataFixtures\ORM\LoadRole;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class ContributionTest extends BaseTestCase {

    protected function getFixtures() {
        return [
            LoadPeriodical::class,
            LoadPerson::class,
            LoadRole::class
        ];
    }

    public function testGetPublicationId() {
        $contribution = new Contribution();
        $contribution->setPerson($this->getReference('person.1'));
        $contribution->setRole($this->getReference('role.1'));
        $contribution->setPublication($this->getReference('periodical.1'));

        $this->assertEquals($this->getReference('periodical.1')->getId(), $contribution->getPublicationId());
    }
}