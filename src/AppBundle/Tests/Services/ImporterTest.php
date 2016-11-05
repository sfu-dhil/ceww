<?php

namespace AppBundle\Tests\Services;

use AppBundle\Services\Importer;
use AppBundle\Tests\Utilities\AbstractTestCase;

class ImporterTest extends AbstractTestCase {

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

    /**
     * @dataProvider processTitleData
     */
    public function testProcessTitle($data, $expected) {
        $matches = $this->importer->processTitle($data);
        foreach ($expected as $k => $v) {
            if( ! $v) {
                continue;
            }
            $this->assertArrayHasKey($k, $matches);
            $this->assertEquals($v, $matches[$k]);
        }
    }

    public function processTitleData() {
        return [
            [
                // data set 0
                'Wanted, a Wife',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '',
                    'genre' => ''
                ]
            ],
            [
                // data set 1
                'Wanted, a Wife (n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 2
                'Wanted, a Wife (Toronto, n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 3
                'Wanted, a Wife (Toronto, ON, n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 4
                'Wanted, a Wife (Toronto, ON: Pattison, n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 5
                'Wanted, a Wife(n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 6
                'Wanted, a Wife(Toronto, n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 7
                'Wanted, a Wife(Toronto, ON, n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 8
                'Wanted, a Wife(Toronto, ON: Pattison, n.d.)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 9                
                'Wanted, a Wife {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 10
                'Wanted, a Wife (n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 11
                'Wanted, a Wife (Toronto, n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '',
                    'genre' => ''
                ]
            ],
            [
                // data set 12
                'Wanted, a Wife (Toronto, ON, n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 13
                'Wanted, a Wife (Toronto, ON: Pattison, n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 14
                'Wanted, a Wife {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 15
                'Wanted, a Wife (n.d.) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 16
                'Wanted, a Wife (Toronto, n.d.) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 17
                'Wanted, a Wife (Toronto, ON, n.d.) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 18
                'Wanted, a Wife (Toronto, ON: Pattison, n.d.) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 19
                'Wanted, a Wife{drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 20
                'Wanted, a Wife(n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 21
                'Wanted, a Wife(Toronto, n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 22
                'Wanted, a Wife(Toronto, ON, n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 23
                'Wanted, a Wife(Toronto, ON: Pattison, n.d.) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 24
                '<Wanted, a Wife http://example.com/path/to/stuff>',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => ''
                ]
            ],
            [
                // data set 25
                '<Wanted, a Wife http://example.com/path/to/stuff> (n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 26
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => '',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 27
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON, n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => '',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 28
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON: Pattison, n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 29
                '<Wanted, a Wife http://example.com/path/to/stuff>(n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 30
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 31
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON, n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 32
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON: Pattison, n.d.)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => ''
                ]
            ],
            [
                // data set 33
                '<Wanted, a Wife http://example.com/path/to/stuff> {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 34
                '<Wanted, a Wife http://example.com/path/to/stuff> (n.d.){drama}',
                [
                    'linked_title' => '',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => ''
                ]
            ],
            [
                // data set 35
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, n.d.){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => ''
                ]
            ],
            [
                // data set 36
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON, n.d.){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 37
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON: Pattison, n.d.){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 38
                '<Wanted, a Wife http://example.com/path/to/stuff> {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 39
                '<Wanted, a Wife http://example.com/path/to/stuff> (n.d.) {drama}',
                [
                    'linked_title' => '',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 40
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, n.d.) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 41
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON, n.d.) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 42
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON: Pattison, n.d.) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 43
                '<Wanted, a Wife http://example.com/path/to/stuff>{drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 44
                '<Wanted, a Wife http://example.com/path/to/stuff>(n.d.){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 45
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, n.d.){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 46
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON, n.d.){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 47
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON: Pattison, n.d.) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => 'n.d.',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 1
                'Wanted, a Wife (1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 2
                'Wanted, a Wife (Toronto, 1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 3
                'Wanted, a Wife (Toronto, ON, 1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 4
                'Wanted, a Wife (Toronto, ON: Pattison, 1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 5
                'Wanted, a Wife(1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 6
                'Wanted, a Wife(Toronto, 1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 7
                'Wanted, a Wife(Toronto, ON, 1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 8
                'Wanted, a Wife(Toronto, ON: Pattison, 1880)',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 10
                'Wanted, a Wife (1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 11
                'Wanted, a Wife (Toronto, 1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 12
                'Wanted, a Wife (Toronto, ON, 1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 13
                'Wanted, a Wife (Toronto, ON: Pattison, 1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 15
                'Wanted, a Wife (1880) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 16
                'Wanted, a Wife (Toronto, 1880) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 17
                'Wanted, a Wife (Toronto, ON, 1880) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 18
                'Wanted, a Wife (Toronto, ON: Pattison, 1880) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 20
                'Wanted, a Wife(1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 21
                'Wanted, a Wife(Toronto, 1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 22
                'Wanted, a Wife(Toronto, ON, 1880){drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 23
                'Wanted, a Wife(Toronto, ON: Pattison, 1880) {drama}',
                [
                    'linked_title' => '',
                    'url' => '',
                    'title' => 'Wanted, a Wife',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 25
                '<Wanted, a Wife http://example.com/path/to/stuff> (1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => '',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 26
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, 1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => '',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 27
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON, 1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => '',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 28
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON: Pattison, 1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 29
                '<Wanted, a Wife http://example.com/path/to/stuff>(1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 30
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, 1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 31
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON, 1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 32
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON: Pattison, 1880)',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 34
                '<Wanted, a Wife http://example.com/path/to/stuff> (1880){drama}',
                [
                    'linked_title' => '',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 35
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, 1880){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => ''
                ]
            ],
            [
                // data set 36
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON, 1880){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 37
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON: Pattison, 1880){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 39
                '<Wanted, a Wife http://example.com/path/to/stuff> (1880) {drama}',
                [
                    'linked_title' => '',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 40
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, 1880) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 41
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON, 1880) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 42
                '<Wanted, a Wife http://example.com/path/to/stuff> (Toronto, ON: Pattison, 1880) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 44
                '<Wanted, a Wife http://example.com/path/to/stuff>(1880){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => '',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 45
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, 1880){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 46
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON, 1880){drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],
            [
                // data set 47
                '<Wanted, a Wife http://example.com/path/to/stuff>(Toronto, ON: Pattison, 1880) {drama}',
                [
                    'linked_title' => 'Wanted, a Wife',
                    'url' => 'http://example.com/path/to/stuff',
                    'title' => '',
                    'loc' => 'Toronto, ON: Pattison',
                    'year' => '1880',
                    'genre' => 'drama'
                ]
            ],        
            [
                '<From Distant Shores: Poems Https://archive.org/details/fromdistantshore00Adam>(Toronto: Briggs, c1898)',
                [
                    'linked_title' => 'From Distant Shores: Poems',
                    'url' => 'Https://archive.org/details/fromdistantshore00Adam',
                    'title' => '',
                    'loc' => 'Toronto: Briggs',
                    'year' => 'c1898',
                    'genre' => ''
                ]
            ]
        ];
    }

}
