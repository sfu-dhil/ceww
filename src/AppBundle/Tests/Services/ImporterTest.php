<?php

namespace AppBundle\Tests\Services;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use AppBundle\entity\Category;
use AppBundle\Tests\Utilities\AbstractTestCase;

class ImporterTest extends AbstractTestCase {

    protected $importer;

    public function setUp() {
        parent::setUp();
        $this->importer = $this->getContainer()->get('ceww.importer');
    }

    public function testSetup() {
        $this->assertInstanceOf('AppBundle\Services\Importer', $this->importer);
    }
    
    public function fixtures() {
        return [
            'AppBundle\Tests\DataFixtures\ORM\LoadCategories',
        ];
    }

    /**
     * @dataProvider processDateData
     */
    public function testProcessDate($str, $year) {
        $this->assertEquals($year, $this->importer->processDate($str));
    }

    public function processDateData() {
        return [
            ['b 1929', 1929],
            ['2012-2014', [2012, 2014]],
            ['02-May-19', 1919],
            ['MAY-19', 1919],
        ];
    }

    /**
     * @dataProvider splitData
     */
    public function testSplit($str, $delim, $alt, $array) {
        $this->assertEquals($array, $this->importer->split($str, $delim, $alt));
    }

    public function splitData() {
        return [
            [null, ';', '', ['']],
            ['', ';', '', ['']],
            ['Foo bar', ';', '', ['Foo bar']],
            ['Foo; bar', ';', '', ['Foo', 'bar']],
            ['P, M; B, W; A, B', ';', '', ['P, M', 'B, W', 'A, B']],
            ['P, M; A B; C D', ';', ',', ['P, M', 'A B', 'C D']],
            ['a; b, c', ';', ',', ['a', 'b, c']],
            ['a; b, c, d', ';', ',', ['a; b', 'c', 'd']],
        ];
    }

    /**
     * @dataProvider cleanPlaceNameData
     */
    public function testCleanPlaceName($name, $clean) {
        $this->assertEquals($clean, $this->importer->cleanPlaceName($name));
    }

    public function cleanPlaceNameData() {
        return [
            ['Toronto, Ontario', 'Toronto, Ontario'],
            ['"Sommersville," Toronto', 'Toronto'],
            ['Toronto "Sommersville" Ontario', 'Toronto "Sommersville" Ontario'],
            ['Toronto (Ontario)', 'Toronto'],
            ['Toronto (1981)', 'Toronto'],
            ['Toronto (1981-1029)', 'Toronto'],
            ['near Yaletown Ontario', 'Yaletown Ontario'],
            [' near Yaletown Ontario', 'Yaletown Ontario'],
            ['Near Yaletown Ontario', 'Yaletown Ontario'],
            ['Nearing, BC', 'Nearing, BC'],
        ];
    }

    /**
     * @dataProvider cleanTitleData
     */
    public function testCleanTitle($title, $clean) {
        $this->assertEquals($clean, $this->importer->cleanTitle($title));
    }

    public function cleanTitleData() {
        return [
            # title casing.
            ['ALL ABOUT CHEESE', 'All About Cheese'],
            ['ABOUT A BOY', 'About A Boy'],
            ['A STRANGE DAY', 'A Strange Day'],
//            # dates
            ['Title (1991) ', 'Title'],
            ['Title (c1991)', 'Title'],
            ['Title (1991-2019)', 'Title'],
            ['Title (c1991-2918)', 'Title'],
            # quotation marks
            ['"CHEESE IT"', 'Cheese It'],
            ['"About the Cat" by Lillian', '"About The Cat" By Lillian'],
        ];
    }

    /**
     * @dataProvider sortableTitleData
     */
    public function testSortableTitle($title, $sortable) {
        $this->assertEquals($sortable, $this->importer->sortableTitle($title));
    }

    public function sortableTitleData() {
        return [
            ['A Dog', 'dog, a'],
            ['The Chicken', 'chicken, the'],
            ['Then and now', 'then and now'],
            ['Abernathy, a story', 'abernathy, a story'],
            ['"Boo" said the girl', 'boo" said the girl'],
            ['Accént eh?', 'accént eh?']
        ];
    }

    /**
     * @dataProvider createAliasData
     */
    public function testCreateAlias($name, Alias $object) {
        $this->assertEquals($object, $this->importer->createAlias($name));
    }

    private function buildAlias($maiden, $name) {
        $alias = new Alias();
        $alias->setMaiden($maiden);
        $alias->setName($name);
        return $alias;
    }

    public function createAliasData() {
        return [
            ["nee Goodfrey", $this->buildAlias(1, "nee Goodfrey")],
            ["née Goodfrey", $this->buildAlias(1, "née Goodfrey")],
            ["Goodfrey Barbican", $this->buildAlias(0, "Goodfrey Barbican")],
        ];
    }

    /**
     * @dataProvider createPlaceData
     */
    public function testCreatePlace($name, Place $place) {
        $this->assertEquals($place, $this->importer->createPlace($name));
    }

    private function buildPlace($name) {
        $place = new Place();
        $place->setName($name);
        return $place;
    }

    public function createPlaceData() {
        return [
            ['Toronto, Canada', $this->buildPlace('Toronto, Canada')]
        ];
    }

    /**
     * @dataProvider createPublicationData
     */
    public function testCreatePublication($title, $type, Publication $publication) {
        $this->assertEquals($publication, $this->importer->createPublication($title, $type));
    }

    private function buildPublication($title, $sortableTitle, $type) {
        $publication = new Publication();
        $publication->setTitle($title);
        $publication->setSortableTitle($sortableTitle);
        $publication->setCategory($type);
        return $publication;
    }

    public function createPublicationData() {
        $category = new Category();
        $category->setLabel('Test');
        return [
            [
                'Chairs and stuff',
                $category,
                $this->buildPublication('Chairs and stuff', 'chairs and stuff', $category)
            ],
        ];
    }
    
    /**
     * @dataProvider setDatesData
     */
    public function testSetDates($bs, $ds, $ebd, $edd) {
        $author = new Author();
        $this->importer->setDates($author, $bs, $ds);
        $this->assertEquals($ebd, $author->getBirthDate());
        $this->assertEquals($edd, $author->getDeathDate());
    }
    public function setDatesData() {
        return [
            ['1894', '1990', '1894', '1990'],
            ['1894-1990', '', '1894', '1990'],
            ['  1894-1990', '', '1894', '1990'],
            ['10 November 1823', '5 November 1898', '1823', '1898'],
            ['Nov-44', 'May-86', '1944', '1986'],
            ['b.1906 Nov 16', '', '1906', ''],
            ['c1787-93', '', '1787', ''], 
            ['1787-', '', '1787', ''], 
            ['1918-living', '', '1918', ''],
            ['24-Jul-54', '3 June 1871', '1954', '1871'],
        ];
    }
    
    /**
     * @dataProvider setPlacesData
     */
    public function testSetPlaces($bs, $ds, $ebs, $eds) {
        $author = new Author();
        $this->importer->setPlaces($author, $bs, $ds);
        $this->assertEquals($ebs, $author->getBirthPlace()->getName());
        $this->assertEquals($eds, $author->getDeathPlace()->getName());
    }
    
    public function setPlacesData() {
        return [
            ['Vic, BC', 'Van, BC', 'Vic, BC', 'Van, BC'],
            ['near Chesik, Ontario', 'x', 'Chesik, Ontario', 'x'],
            ['Chesik (Chessick), Oxford', 'x', 'Chesik (Chessick), Oxford', 'x'],
            ['Chesik, Oxford (1999)', 'x', 'Chesik, Oxford', 'x'],
        ];
    }

    /**
     * @dataProvider setAliasesData
     */
    public function testSetAliases($aliasStr, $aliases) {
        $author = new Author();
        $this->importer->setAliases($author, $aliasStr);
        $this->assertCount(count($aliases), $author->getAliases());
        foreach($author->getAliases() as $alias) {
            $this->assertContains($alias->getName(), $aliases);
        }
    }    
    public function setAliasesData() {
        return [
            ['Lady M.', ['Lady M.']],
            ['Lady M.; Lady B.; Lady J', ['Lady M.', 'Lady B.', 'Lady J']],
            ['Lady M., Lady B., Lady J', ['Lady M.', 'Lady B.', 'Lady J']],
        ];
    }
    
    /**
     * @dataProvider setResidencesData
     */
    public function testSetResidences($residencesStr, $residences) {
        $author = new Author();
        $this->importer->setResidences($author, $residencesStr);
        $this->assertCount(count($residences), $author->getResidences());
        foreach($author->getResidences() as $residence) {
            $this->assertContains($residence->getName(), $residences);
        }
    }
    
    public function setResidencesData() {
        return [
            ['Bramford, ON', ['Bramford, ON']],
            ['Vic, BC; Van, BC', ['Vic, BC', 'Van, BC']],
            ['Winnipeg, Manitoba (1885-)', ['Winnipeg, Manitoba']],
        ];
    }
    
    /**
     * @dataProvider setPublicationsData
     */
    public function testSetPublications($pubStr, $type, $pubs) {
        $author = new Author();
        $this->importer->setPublications($author, $pubStr, $type);
        $this->assertCount(count($pubs), $author->getPublications());
        foreach($author->getPublications() as $publication) {
            $this->assertContains($publication->getTitle(), $pubs);
        }
    }
    public function setPublicationsData() {
        return [
            ['How now', 'Book', ['How now']],
        ];
    }
}
