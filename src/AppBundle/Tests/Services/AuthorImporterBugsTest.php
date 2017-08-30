<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Periodical;
use AppBundle\Entity\Person;
use AppBundle\Services\AuthorImporter;
use AppBundle\Tests\DataFixtures\ORM\LoadRole;
use AppBundle\Tests\Util\BaseTestCase;
use Exception;

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

    public function testHarriet() {
        $this->importer->setCommit(true);
        $row = [];
        $row[] = "Jennings, Clotilda";
        $row[] = "1830c";
        $row[] = "Halifax, NS";
        $row[] = "1895c";
        $row[] = "Montreal, QC";
        $row[] = "Maude; Maude Alma; Mileta";
        $row[] = "Halifax, NS (c1830-); Montreal, QC (1881, 1891)";
        $row[] = 'LINDEN RHYMES (1854); "THE WHITE ROSE IN ACADIA" AND "AUTUMN IN NOVA SCOTIA" A PRIZE TALE AND POEM (1855); ISABEL LEICESTER: A ROMANCE (1874); NORTH MOUNTAIN, NEAR GRAND PRE (1883)';
        $row[] = "Whyte-Edgar, WREATH OF CANADIAN SONG (1910)";
        $row[] = "CANADIAN DOMINION ILLUSTRATED; PROVINCIAL, OR HALIFAX MONTHLY MAGAZINE; HAMILTON SPECTATOR; SATURDAY READER";
        try {
            $this->importer->importRow($row);
        } catch (Exception $e) {
            $this->fail('Caught exception: ' . $e->getMessage());
        }
        $this->assertEquals(1, count($this->em->getRepository(Person::class)->findAll()));
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
