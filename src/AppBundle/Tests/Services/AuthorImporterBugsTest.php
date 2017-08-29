<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Periodical;
use AppBundle\Services\AuthorImporter;
use AppBundle\Tests\DataFixtures\ORM\LoadRole;
use AppBundle\Tests\Util\BaseTestCase;

class AuthorImporterBugsTest extends BaseTestCase {

    /**
     * @var AuthorImporter
     */
    protected $importer;

    public function setUp() {
        parent::setUp();
        $this->importer = $this->getContainer()->get('ceww.importer.author');
    }

    protected function getFixtures() {
        return array(
            LoadRole::class,
        );
    }
    
    // Title in the cell, twice.
    public function testDupeData() {
        $row = array_fill(0, 13, '');
        $row[9] = 'Alberta Poetry Yearbook (1936); Alberta Poetry Yearbook (1938)';
        
        $this->importer->setCommit(true);
        $this->importer->importRow($row);
        $periodicals = $this->em->getRepository(Periodical::class)->findAll();
        $this->assertEquals(1, count($periodicals));
    }
    
    // Crazy title cases.
    public function testCaseInsensitive() {
        $row = array_fill(0, 13, '');
        $row[9] = 'Alberta Poetry Yearbook (1936); ALBERTA POETRY YEARBOOK (1938)';
        
        $this->importer->setCommit(true);
        $this->importer->importRow($row);
        $periodicals = $this->em->getRepository(Periodical::class)->findAll();
        $this->assertEquals(1, count($periodicals));
    }

}
