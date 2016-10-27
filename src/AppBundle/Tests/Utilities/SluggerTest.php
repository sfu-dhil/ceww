<?php

namespace AppBundle\Tests\Utilities;

use AppBundle\Tests\Utilities\AbstractTestCase;
use AppBundle\Utilities\Slugger;

class SluggerTest extends AbstractTestCase
{

    /**
     * @var Slugger
     */
    protected $slugger;

    public function setUp() {
        parent::setUp();
        $this->slugger = new Slugger();
    }

    public function testSetup() {
        $this->assertInstanceOf(Slugger::class, $this->slugger);
    }

    /**
     * @dataProvider slugData
     */
    public function testSlug($str, $expected) {
        $this->assertEquals($expected, $this->slugger->slug($str));
    }

    public function slugData() {
        return [
            [null, null],
            ['', ''],
            ['The Dinner', 'the-dinner'],
            ['  The Dinner  ', 'the-dinner'],
            [' Röb ', 'rob'],
            ['Robero . . .', 'robero'],
            ['Robero...', 'robero'],
            ['Rœb', 'roeb'],
            ['strauß', 'strauss'],
            ['Part 2.1', 'part-2.1'],
            ['Question? Yes.', 'question-yes'],
            ['Question #1a', 'question-1a'],
            ['Question-1', 'question-1'],
            ['Q -1', 'q-1'],
            ['Q_1', 'q_1'],
        ];
    }
    
    /**
     * @dataProvider slugSeparatorData
     */
    public function testSlugSeparator($str, $expected, $separator) {
        $this->assertEquals($expected, $this->slugger->slug($str, $separator));        
    }
    
    public function slugSeparatorData() {
        return [
            [null, null, null],
            ['', '', '.'],
            ['The Dinner', 'the-dinner', '-'],
            ['Part 2.1', 'part_2.1', '_'],
            ['Question? Yes.', 'question.yes', '.'],
            ['Question? Yes.', 'question/yes', '/'],
            ['Question #1a', 'question1a', ''],
            ['Multi char seps', 'multi---char---seps', '---'],
            ['Mash Words', 'mashwords', null],
            ['Mash Words', 'mashwords', ''],
        ];
    }
}
