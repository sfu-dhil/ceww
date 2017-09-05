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

    public function testSetup() {
        $this->assertInstanceOf(AuthorImporter::class, $this->importer);
    }

    /**
     * @dataProvider trimData
     */
    public function testTrim($expected, $data) {
        $this->assertEquals($expected, $this->importer->trim($data));
    }

    public function trimData() {
        return array(
            ['', ''],
            ['x', 'x'],
            ['x', ' x '],
            ['Robsonstrauß', ' Robsonstrauß '],
        );
    } 

    /**
     * @dataProvider splitData
     */
    public function testSplit($expected, $data, $delim = ';') {
        $this->assertEquals($expected, $this->importer->split($data, $delim));
    }

    public function splitData() {
        return array(
            [[], ''],
            [['abc'], 'abc'],
            [['abc'], 'abc', ';'],
            [['abc', 'def'], 'abc;def'],
            [['abc', 'def'], 'abc;def', ';'],
            [['abc', 'def'], 'abc,def', ','],
            [['abc', 'def'], 'abc; def'],
            [['abc', 'def'], ' abc; def '],
            [['a bc', 'de f'], 'a bc; de f'],
        );
    }

    /**
     * @dataProvider createPersonData
     */
    public function testCreatePerson($expectedName, $name) {
        $person = $this->importer->createPerson($name);
        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals($expectedName, $person->getFullName());
    }

    public function createPersonData() {
        return array(
            ['(unknown)', null],
            ['(unknown)', ''],
            ['(unknown)', false],
            ['abc', 'abc'],
            ['the doctor', 'the doctor'],
            ['martha jones', 'jones, martha'],
        );
    }

    /**
     * @dataProvider getPlaceData
     */
    public function testGetPlace($expected, $value, $null = false) {
        $place = $this->importer->getPlace($value);
        if ($null) {
            $this->assertNull($place);
        } else {
            $this->assertInstanceOf(Place::class, $place);
            $this->assertEquals($expected, $place->getName());
        }
    }

    public function getPlaceData() {
        return array(
            ['victoria, bc', 'victoria, bc'],
            ['vancouver, bc', ' vancouver, bc '],
            ['québec, qc', ' québec, qc '],
            ['victoria', 'victoria (1901)'],
            ['victoria', 'victoria (1901,1902)'],
            ['victoria', 'victoria (1901-1902)'],
            ['victoria', 'victoria (1901) '],
            ['victoria', 'victoria (1901 , 1902) '],
            ['victoria', 'victoria (1901 - 1902) '],
            ['', '', true],
            ['', '  ', true],
            ['', '  (1902)', true],
        );
    }

    /**
     * @dataProvider setBirthDateData
     * 
     * Most of the DateYear stuff is tested elsewhere.
     */
    public function testSetBirthdate($expected, $value, $null = false) {
        $person = new Person();
        $this->importer->setBirthDate($person, $value);
        if ($null) {
            $this->assertNull($person->getBirthDate());
        } else {
            $date = $person->getBirthDate();
            $this->assertInstanceOf(DateYear::class, $date);
            $this->assertEquals($expected, (string) $date);
        }
    }

    public function setBirthDateData() {
        return array(
            ['', '', true],
            ['', '   ', true],
            ['1901', ' 1901'],
            ['c1901', 'c1901'],
            ['1901', '  1901 '],
            ['c1901', ' c1901 '],
        );
    }

    /**
     * @dataProvider setDeathDateData
     * 
     * Most of the DateYear stuff is tested elsewhere.
     */
    public function testSetDeathdate($expected, $value, $null = false) {
        $person = new Person();
        $this->importer->setDeathDate($person, $value);
        if ($null) {
            $this->assertNull($person->getDeathDate());
        } else {
            $date = $person->getDeathDate();
            $this->assertInstanceOf(DateYear::class, $date);
            $this->assertEquals($expected, (string) $date);
        }
    }

    public function setDeathDateData() {
        return array(
            ['', '', true],
            ['', '   ', true],
            ['1901', ' 1901'],
            ['c1901', 'c1901'],
            ['1901', '  1901 '],
            ['c1901', ' c1901 '],
        );
    }

    /**
     * @dataProvider setBirthPlaceData 
     */
    public function testSetBirthPlace($expected, $place) {
        $person = new Person();
        $person->setBirthPlace($place);
        $this->assertEquals($expected, $place);
    }

    public function setBirthPlaceData() {
        $place = new Place();
        return array(
            [null, null],
            [$place, $place],
        );
    }

    /**
     * @dataProvider setDeathPlaceData 
     */
    public function testSetDeathPlace($expected, $place) {
        $person = new Person();
        $person->setDeathPlace($place);
        $this->assertEquals($expected, $place);
    }

    public function setDeathPlaceData() {
        $place = new Place();
        return array(
            [null, null],
            [$place, $place],
        );
    }

    /**
     * @dataProvider getAliasData
     */
    public function testGetAlias($expected, $maiden, $name) {
        $alias = $this->importer->getAlias($name);
        $this->assertInstanceOf(Alias::class, $alias);
        $this->assertEquals($maiden, $alias->getMaiden());
        $this->assertEquals($expected, $alias->getName());
    }

    public function getAliasData() {
        return array(
            ['may', false, 'may'],
            ['needham', false, 'needham'],
            ['may', true, 'nee may'],
            ['may', true, 'née may'],
            ['may', true, 'née     may'],
        );
    }

    /**
     * @dataProvider addAliasesData
     */
    public function testAddAliases($expected, $value) {
        $person = new Person();
        $this->importer->addAliases($person, $value);
        $this->assertEquals(count($expected), count($person->getAliases()));
        foreach ($person->getAliases() as $key => $value) {
            $this->assertEquals($expected[$key], $value->getName());
        }
    }

    public function addAliasesData() {
        return array(
            [[], ''],
            [[], '  '],
            [['needham'], 'needham'],
            [['may'], 'may'],
            [['may', 'june'], 'may; june'],
            [['may', 'june'], ' may;june ']
        );
    }

    /**
     * @dataProvider addMaidenAliasesData
     */
    public function testAddMaidenAliases($expected, $value) {
        $person = new Person();
        $this->importer->addAliases($person, $value);
        $this->assertEquals(count($expected), count($person->getAliases()));
        foreach ($person->getAliases() as $key => $value) {
            $this->assertEquals($expected[$key], $value->getName());
        }
    }

    public function addMaidenAliasesData() {
        return array(
            [['may'], 'nee may'],
            [['may', 'june'], 'nee may; nee june'],
            [['may', 'june'], ' nee may;nee june '],
            [['may'], 'née may'],
            [['may', 'june'], 'née may; née june'],
            [['may', 'june'], ' née may;née june ']
        );
    }

    /**
     * @dataProvider addResidencesData
     */
    public function testAddResidences($expected, $value) {
        $person = new Person();
        $this->importer->addResidences($person, $value);
        $this->assertEquals(count($expected), count($person->getResidences()));
        foreach ($person->getResidences() as $key => $residence) {
            $this->assertEquals($expected[$key], $residence->getName());
        }
    }

    public function addResidencesData() {
        return array(
            [[], ''],
            [[], null],
            [['vancouver'], 'vancouver'],
            [['vancouver'], 'vancouver (1901)'],
            [['vancouver'], 'vancouver (1901,1902)'],
            [['vancouver'], 'vancouver (1901-1902)'],
            [['vancouver'], 'vancouver (c1901-c1902)'],
            [['vancouver', 'montreal'], 'vancouver; montreal'],
            [['vancouver', 'montreal'], 'vancouver (1901); montreal'],
            [['vancouver', 'montreal'], 'vancouver (1901,1902); montreal'],
            [['vancouver', 'montreal'], 'vancouver (1901-1902); montreal'],
            [['vancouver', 'montreal'], 'vancouver (c1901-c1902); montreal'],
            [['vancouver', 'montreal'], 'vancouver;montreal'],
            [['vancouver', 'montreal'], 'vancouver (1901);montreal'],
            [['vancouver', 'montreal'], 'vancouver (1901,1902);montreal'],
            [['vancouver', 'montreal'], 'vancouver (1901-1902);montreal'],
            [['vancouver', 'montreal'], 'vancouver (c1901-c1902);montreal'],
        );
    }

    /**
     * @dataProvider titleDateData
     */
    public function testTitleDate($expected, $title) {
        $result = $this->importer->titleDate($title);
        $this->assertEquals($expected, $result);
    }

    public function titleDateData() {
        return array(
            [[null, null], ''],
            [['foo', null], 'foo'],
            [['foo', null], 'foo (n.d.)'],
            [['foo', 'c1901'], 'foo (c1901)'],
            [['foo', '1901,1902'], 'foo  (1901,1902)'],
            [['foo', '1901'], 'foo [1901]'],
            [['foo', '1901,1903'], 'foo [1901,1903]'],
            [['foo', '1901-1903'], 'foo [1901-1903]'],
            [['foo', '1901;1903'], 'foo [1901;1903]'],
            [['foo', '1901-2'], 'foo [1901-2]'],
            [['Hæmochromatosis', '1900'], 'Hæmochromatosis (1900)'],

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
            
        );
    } 

    /**
     * @dataProvider titlePlaceData
     */
    public function testTitlePlace($expected, $title) {
        $result = $this->importer->titlePlace($title);
        $this->assertEquals($expected, $result);
    }

    public function titlePlaceData() {
        return array(
            [[null, null], ''],
            [['foo', null], 'foo'],
            [['foo', 'delaware'], 'foo (delaware)'],
            [['foo', 'london'], 'foo  (london)'],
            [['Hæmochromatosis', 'Canada'], 'Hæmochromatosis (Canada)']
        );
    }

    /**
     * @dataProvider getBookData
     */
    public function testBook($expected, $title, $date, $placeName) {
        $publication = $this->importer->getBook($title, $date, $placeName);
        $this->assertInstanceOf(Book::class, $publication);
        $this->assertEquals($expected[0], $publication->getTitle());
        $this->assertEquals($expected[1], $publication->getSortableTitle());
        if ($date) { 
            $this->assertEquals($date, (string) $publication->getDateYear());
        } else {
            $this->assertNull($publication->getDateYear());
        }
        if ($placeName) {
            $this->assertEquals($placeName, $publication->getLocation()->getName());
        } 
    } 

    public function getBookData() {
        return array(
            [['The Title', 'title, the', null, null], 'The Title', null, null],
            [['Title Stuffs', 'title stuffs', null, null], 'Title Stuffs', null, null],
            [['The Title', 'title, the', '1901', null], 'The Title', '1901', null],
            [['The Title', 'title, the', null, 'vancouver'], 'The Title', null, 'vancouver'],            
            [['Hæmochromatosis', 'hæmochromatosis', null, null],  'Hæmochromatosis', null, null],
        );
    }

    /**
     * @dataProvider getCompilationData
     */
    public function testGetCompilation($expected, $title, $date, $placeName) {
        $publication = $this->importer->getCompilation($title, $date, $placeName);
        $this->assertInstanceOf(Compilation::class, $publication);
        $this->assertEquals($expected[0], $publication->getTitle());
        $this->assertEquals($expected[1], $publication->getSortableTitle());
        if ($date) { 
            $this->assertEquals($date, (string) $publication->getDateYear());
        } else {
            $this->assertNull($publication->getDateYear());
        }
        if ($placeName) {
            $this->assertEquals($placeName, $publication->getLocation()->getName());
        } 
    } 

    public function getCompilationData() {
        return array(
            [['The Title', 'title, the', null, null], 'The Title', null, null],
            [['Title Stuffs', 'title stuffs', null, null], 'Title Stuffs', null, null],
            [['The Title', 'title, the', '1901', null], 'The Title', '1901', null],
            [['The Title', 'title, the', null, 'vancouver'], 'The Title', null, 'vancouver'],            
            [['Hæmochromatosis', 'hæmochromatosis', null, null],  'Hæmochromatosis', null, null],
        );
    }

    /**
     * @dataProvider getPeriodicalData
     */
    public function testGetPeriodical($expected, $title, $date) {
        $publication = $this->importer->getPeriodical($title, $date);
        $this->assertInstanceOf(Periodical::class, $publication);
        $this->assertEquals($expected[0], $publication->getTitle());
        $this->assertEquals($expected[1], $publication->getSortableTitle());
        $this->assertNull($publication->getDateYear());
    } 

    public function getPeriodicalData() {
        return array(
            [['The Title', 'title, the'], 'The Title'],
            [['Title Stuffs', 'title stuffs'], 'Title Stuffs'],
            [['The Title', 'title, the'], 'The Title'],
            [['The Title', 'title, the'], 'The Title'],            
            [['Hæmochromatosis', 'hæmochromatosis'],  'Hæmochromatosis'],
        );
    }
    
    protected function assertContributions($expected, $contributions) {
        $this->assertEquals(count($expected), count($contributions));
        $key = 0;
        foreach ($contributions as $contribution) {
            $publication = $contribution->getPublication();
            $this->assertEquals($expected[$key][0], $publication->getTitle());
            $key++;
        }
    }

    /**
     * @dataProvider importRowData
     */
    public function testImportRow($expected, $row) {
        $this->importer->setCommit(true);
        $person = $this->importer->importRow($row);
        $this->assertEquals($expected['name'], $person->getFullName());
        $this->assertEquals($expected['sortableName'], $person->getSortableName());
        $this->assertEquals($expected['born'][0], (string) $person->getBirthDate());
        $this->assertEquals($expected['born'][1], (string) $person->getBirthPlace());
        $this->assertEquals($expected['died'][0], (string) $person->getDeathDate());
        $this->assertEquals($expected['died'][1], (string) $person->getDeathPlace());

        $residences = $person->getResidences();
        $this->assertEquals(count($expected['residences']), count($residences));
        foreach ($residences as $key => $residence) {
            $this->assertEquals($expected['residences'][$key], (string) $residence);
        }

        $this->assertContributions($expected['books'], $person->getContributions(Publication::BOOK));
        $this->assertContributions($expected['collections'], $person->getContributions(Publication::COMPILATION));
        $this->assertContributions($expected['periodicals'], $person->getContributions(Publication::PERIODICAL));
    }

    public function importRowData() {
        return array(
            array(
                array(
                    'name' => 'Ishbel Aberdeen',
                    'sortableName' => 'aberdeen, ishbel',
                    'born' => ['1857', 'London, England'],
                    'died' => ['1939', 'Aberdeenshire, Scotland'],
                    'aliases' => [
                        ['Marjoribanks', true],
                        ['Ishbel Gordon', false],
                    ],
                    'residences' => [
                        'Aberdeenshire, Scotland',
                        'Ireland',
                    ],
                    'books' => [
                        ['Through Canada with a Kodak', '1893'],
                        ['The Sorrows of Ireland', '1916'],
                    ],
                    'collections' => [
                        ['Blumer, Bedside Diagnosis', '1928'],
                    ],
                    'periodicals' => [
                        ['Onward: A Paper for Young Canadians'],
                        ['Upward'],
                        ['Yale Review'],
                    ]
                ),
                array(
                    0 => 'Aberdeen, Ishbel',
                    1 => '1857',
                    2 => 'London, England',
                    3 => '1939',
                    4 => 'Aberdeenshire, Scotland',
                    5 => 'née Marjoribanks; Ishbel Gordon',
                    6 => 'Aberdeenshire, Scotland; ?, Ireland',
                    7 => 'Through Canada with a Kodak (1893); The Sorrows of Ireland (1916)',
                    8 => 'Blumer, Bedside Diagnosis (1928)',
                    9 => 'Onward: A Paper for Young Canadians; Upward; Yale Review',
                    10 => 'This is a test entry',
                    11 => 'Note 1',
                    12 => 'Note 2',
                ),
            ),
            
//Abbott, Maude Elizabeth Seymour 	1869	Saint Andrews East, QC	1940	Montreal, QC	née Maude Elizabeth Seymour Babin	Birmingham, West Midlands, England; 
// A Rare Form of Pyosalpinx Complicating Uterine Myoma (1900); Pigmentation Cirrhosis of the Liver in a Case of Hæmochromatosis (1900); Museum Notes (1901); 
//
            array(
                array(
                    'name' => 'Maude Elizabeth Seymour Abbott',
                    'sortableName' => 'abbott, maude elizabeth seymour',
                    'born' => ['1869', 'Saint Andrews East, QC'],
                    'died' => ['1940', 'Montreal, QC'],
                    'aliases' => [],
                    'residences' => [],
                    'books' => [
                        ['A Rare Form of Pyosalpinx Complicating Uterine Myoma', '1900'],
                        ['Pigmentation Cirrhosis of the Liver IN a Case of Hæmochromatosis', '1900'],
                        ['Museum Notes', '1901'],
                    ],
                    'collections' => [],
                    'periodicals' => []
                ),
                array(
                    0 => 'Abbott, Maude Elizabeth Seymour',
                    1 => '1869',
                    2 => 'Saint Andrews East, QC',
                    3 => '1940',
                    4 => 'Montreal, QC',
                    5 => '',
                    6 => '',
                    7 => 'A Rare Form of Pyosalpinx Complicating Uterine Myoma (1900); Pigmentation Cirrhosis of the Liver in a Case of Hæmochromatosis (1900); Museum Notes (1901)',
                    8 => '',
                    9 => '',
                    10 => '',
                    11 => '',
                    12 => '',
                ),
            ),            
        );
    }

}
