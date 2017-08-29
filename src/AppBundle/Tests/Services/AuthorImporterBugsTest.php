<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Book;
use AppBundle\Entity\Compilation;
use AppBundle\Entity\DateYear;
use AppBundle\Entity\Periodical;
use AppBundle\Entity\Person;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use AppBundle\Services\AuthorImporter;
use AppBundle\Tests\DataFixtures\ORM\LoadRole;
use AppBundle\Tests\Util\BaseTestCase;

class AuthorImporterTest extends BaseTestCase {

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
//    public function testDupeData() {
//        $row = array_fill(0, 13, '');
//        $row[9] = 'Alberta Poetry Yearbook (1936); Alberta Poetry Yearbook (1938)';
//        
//        $this->importer->setCommit(true);
//        $this->importer->importRow($row);
//        $periodicals = $this->em->getRepository(Periodical::class)->findAll();
//        $this->assertEquals(1, count($periodicals));
//    }

    /**
     * @dataProvider badDatesData
     * @param type $expected
     * @param type $value
     */
    public function testBadDates($expected, $value) {
        list($title, $date) = $this->importer->titleDate($value);
        $this->assertEquals($expected[0], $title);
        $this->assertEquals($expected[1], $date);
    }
    
    public function badDatesData() {
        return [
            [['AB', null], 'AB'],
            [['AB', '1945'], 'AB (1945)'],
            [['AB', '1945; 1946'], 'AB (1945; 1946)'],
            [['AB', '1945; 1946; 1966'], 'AB (1945; 1946; 1966)'],
            [['AB', '1945, 1946'], 'AB (1945, 1946)'],
            [['AB', '1945, 1946, 1949'], 'AB (1945, 1946, 1949)'],
            [['AB', '1945-1946'], 'AB (1945-1946)'],
            [['AB', '1945-6'], 'AB (1945-6)'],

            [['AB', '1945'], 'AB [1945]'],
            [['AB', '1945; 1946'], 'AB [1945; 1946]'],
            [['AB', '1945; 1946; 1966'], 'AB [1945; 1946; 1966]'],
            [['AB', '1945, 1946'], 'AB [1945, 1946]'],
            [['AB', '1945, 1946, 1949'], 'AB [1945, 1946, 1949]'],
            [['AB', '1945-1946'], 'AB [1945-1946]'],
            [['AB', '1945-6'], 'AB [1945-6]'],
            
            [['AB', '1945'], 'AB ([1945])'],
            [['AB', '1945; 1946'], 'AB ([1945; 1946])'],
            [['AB', '1945; 1946; 1966'], 'AB ([1945; 1946; 1966])'],
            [['AB', '1945, 1946'], 'AB ([1945, 1946])'],
            [['AB', '1945, 1946, 1949'], 'AB ([1945, 1946, 1949])'],
            [['AB', '1945-1946'], 'AB ([1945-1946])'],
            [['AB', '1945-6'], 'AB ([1945-6])'],
            
            [['The Wind (Fire)', null], 'The Wind (Fire)'],
        ];
    }
    
    // Crazy title cases.
//    public function testCaseInsensitive() {
//        $row = array_fill(0, 13, '');
//        $row[9] = 'Alberta Poetry Yearbook (1936); ALBERTA POETRY YEARBOOK (1938)';
//        
//        $this->importer->setCommit(true);
//        $this->importer->importRow($row);
//        $periodicals = $this->em->getRepository(Periodical::class)->findAll();
//        $this->assertEquals(1, count($periodicals));
//    }

}
