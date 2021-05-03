<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\Contribution;
use App\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase {
    public function testAddContribution() : void {
        $role = new Role();
        $contribution = new Contribution();

        $role->addContribution($contribution);
        $this->assertCount(1, $role->getContributions());
    }

    public function testRemoveContribution() : void {
        $role = new Role();
        $contribution = new Contribution();

        $role->addContribution($contribution);
        $role->removeContribution($contribution);

        $this->assertCount(0, $role->getContributions());
    }
}
