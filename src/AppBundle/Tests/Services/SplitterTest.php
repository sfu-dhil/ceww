<?php

namespace AppBundle\Tests\Services;

use AppBundle\Services\Splitter;
use Nines\UtilBundle\Tests\Util\BaseTestCase;
use Exception;

class ImporterTest extends BaseTestCase {

    /**
     * @var Splitter
     */
    private $splitter;

    public function setUp() {
        parent::setUp();
        $this->splitter = $this->getContainer()->get(Splitter::class);
    }

    public function testSetup() {
        $this->assertInstanceOf(Splitter::class, $this->splitter);
    }
    
    /**
     * @dataProvider splitData
     */
    public function testSplit($string, $expected) {
        $this->assertEquals($expected, $this->splitter->split($string));
    }
    
    public function splitData() {
        return [
            ['The Whale', ['The Whale']],
            ['The Whale; The Other Whale', ['The Whale', 'The Other Whale']],
            ['The Whale (); The Other Whale', ['The Whale ()', 'The Other Whale']],
            ['The Whale []; The Other Whale', ['The Whale []', 'The Other Whale']],
            ['The Whale (1800); The Other Whale', ['The Whale (1800)', 'The Other Whale']],
            ['The Whale (1800; 1801); The Other Whale', ['The Whale (1800; 1801)', 'The Other Whale']],
            ['The Whale [1800; 1801]; The Other Whale', ['The Whale [1800; 1801]', 'The Other Whale']],
            ['Frappé Coffee', ['Frappé Coffee']],
            ['Coffee; Frappé Coffee', ['Coffee', 'Frappé Coffee']],
        ];
    }
    
    /**
     * @dataProvider splitExceptionData
     * @expectedException Exception
     */
    public function testSplitExceptions($string) {
        $this->splitter->split($string);
    }

    public function splitExceptionData() {
        return [
            ['The Whale (())'],
            ['The Whale ([])'],
            ['The Whale [[]]'],
            ['The Whale [()]'],
            
            ['The Whale ( Or; (a big fish.))'],
            ['The Whale ([1980;1900])'],
        ];
    }

}
