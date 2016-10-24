<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use AppBundle\Entity\Category;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use AppBundle\Services\Importer;
use AppBundle\Tests\Utilities\AbstractTestCase;

class ImporterTest extends AbstractTestCase
{

    /**
     * @var Importer
     */
    protected $importer;

    public function setUp() {
        parent::setUp();
        $this->importer = $this->getContainer()->get('ceww.importer');
    }

    public function testSetup() {
        $this->assertInstanceOf('AppBundle\Services\Importer', $this->importer);
    }


}
