<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\DateYear;
use Exception;
use PHPUnit\Framework\TestCase;

class DateYearTest extends TestCase {
    /**
     * @dataProvider setValueData
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testSetValue($expected, $value) {
        $date = new DateYear();
        $date->setValue($value);
        $this->assertEquals($expected, $date->getValue());
    }

    public function setValueData() {
        return array(
            array('1800', '1800'),
            array('c1800', 'c1800'),
            array('c1800', 'C1800'),
            array('c1800', ' c 1800 '),

            array('1800-', '1800-'),
            array('1800-', '1800 - '),
            array('c1800-', 'c1800-'),
            array('c1800-', 'C1800-'),
            array('c1800-', ' c 1800 - '),

            array('-1800', '-1800'),
            array('-1800', ' - 1800'),
            array('-c1800', '-c1800'),
            array('-c1800', '-C1800'),
            array('-c1800', ' - c1800'),

            array('1800-1801', '1800-1801'),
            array('c1800-1801', 'c1800-1801'),
            array('1800-c1801', '1800-c1801'),
            array('c1800-c1801', 'c1800-c1801'),
            array('c1800-c1801', 'C1800-C1801'),

            array('1800-1801', ' 1800 - 1801 '),
            array('c1800-1801', ' c 1800 - 1801 '),
            array('1800-c1801', ' 1800 - c 1801 '),
            array('c1800-c1801', ' c 1800 - c 1801 '),
            array('c1800-c1801', ' C 1800 - C 1801 '),
        );
    }

    /**
     * @dataProvider setBadValueData
     *
     * @param mixed $value
     */
    public function testSetBadValue($value) {
        $this->expectException(Exception::class);
        $date = new DateYear();
        $date->setValue($value);
        $this->fail('Set value did not throw an exception.');
    }

    public function setBadValueData() {
        return array(
            array(null),
            array(false),
            array(true),
            array('cheese'),
            array('180'),
            array('c180'),
            array('-180'),
            array('-19999'),
            array(''),
            array('1990-1991-1992'),
            array('x1989'),
        );
    }

    /**
     * @dataProvider rangeData
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testIsRange($expected, $value) {
        $date = new DateYear();
        $date->setValue($value);
        $this->assertEquals($expected, $date->isRange());
    }

    public function rangeData() {
        return array(
            array(false, 1800),
            array(false, '1800'),
            array(false, 'c1800'),
            array(false, 'C1799'),

            array(true, -1800),
            array(true, '-1800'),
            array(true, '-c1800'),
            array(true, '-C1800'),

            array(true, '1800-'),
            array(true, 'c1800-'),
            array(true, 'C1800-'),

            array(true, '1800-1805'),
            array(true, 'c1800-1805'),
            array(true, '1800-c1805'),
            array(true, 'c1800-c1805'),
            array(true, '1800-1805'),
            array(true, 'C1800-1805'),
            array(true, '1800-C1805'),
            array(true, 'C1800-C1805'),
        );
    }

    /**
     * @dataProvider hasStartData
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testHasStart($expected, $value) {
        $date = new DateYear();
        $date->setValue($value);
        $this->assertEquals($expected, $date->hasStart());
    }

    public function hasStartData() {
        return array(
            array(true, 1800),
            array(true, '1800'),
            array(true, 'c1800'),
            array(true, 'C1799'),

            array(false, -1800),
            array(false, '-1800'),
            array(false, '-c1800'),
            array(false, '-C1800'),

            array(true, '1800-'),
            array(true, 'c1800-'),
            array(true, 'C1800-'),

            array(true, '1800-1805'),
            array(true, 'c1800-1805'),
            array(true, '1800-c1805'),
            array(true, 'c1800-c1805'),
            array(true, '1800-1805'),
            array(true, 'C1800-1805'),
            array(true, '1800-C1805'),
            array(true, 'C1800-C1805'),
        );
    }

    /**
     * @dataProvider getStartData
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testGetStart($expected, $value) {
        $date = new DateYear();
        $date->setValue($value);
        $this->assertEquals($expected, $date->getStart());
    }

    public function getStartData() {
        return array(
            array('1800', 1800),
            array('1800', '1800'),
            array('c1800', 'c1800'),
            array('c1799', 'C1799'),

            array(null, -1800),
            array(null, '-1800'),
            array(null, '-c1800'),
            array(null, '-C1800'),

            array('1800', '1800-'),
            array('c1800', 'c1800-'),
            array('c1800', 'C1800-'),

            array('1800', '1800-1805'),
            array('c1800', 'c1800-1805'),
            array('1800', '1800-c1805'),
            array('c1800', 'c1800-c1805'),
            array('1800', '1800-1805'),
            array('c1800', 'C1800-1805'),
            array('1800', '1800-C1805'),
            array('c1800', 'C1800-C1805'),
        );
    }

    /**
     * @dataProvider hasEndData
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testHasEnd($expected, $value) {
        $date = new DateYear();
        $date->setValue($value);
        $this->assertEquals($expected, $date->hasEnd());
    }

    public function hasEndData() {
        return array(
            array(true, 1800),
            array(true, '1800'),
            array(true, 'c1800'),
            array(true, 'C1799'),

            array(true, -1800),
            array(true, '-1800'),
            array(true, '-c1800'),
            array(true, '-C1800'),

            array(false, '1800-'),
            array(false, 'c1800-'),
            array(false, 'C1800-'),

            array(true, '1800-1805'),
            array(true, 'c1800-1805'),
            array(true, '1800-c1805'),
            array(true, 'c1800-c1805'),
            array(true, '1800-1805'),
            array(true, 'C1800-1805'),
            array(true, '1800-C1805'),
            array(true, 'C1800-C1805'),
        );
    }

    /**
     * @dataProvider getEndData
     *
     * @param mixed $expected
     * @param mixed $value
     */
    public function testGetEnd($expected, $value) {
        $date = new DateYear();
        $date->setValue($value);
        $this->assertEquals($expected, $date->getEnd());
    }

    public function getEndData() {
        return array(
            array('1800', 1800),
            array('1800', '1800'),
            array('c1800', 'c1800'),
            array('c1799', 'C1799'),

            array('1800', -1800),
            array('1800', '-1800'),
            array('c1800', '-c1800'),
            array('c1800', '-C1800'),

            array('', '1800-'),
            array('', 'c1800-'),
            array('', 'C1800-'),

            array('1805', '1800-1805'),
            array('1805', 'c1800-1805'),
            array('c1805', '1800-c1805'),
            array('c1805', 'c1800-c1805'),
            array('1805', '1800-1805'),
            array('1805', 'C1800-1805'),
            array('c1805', '1800-C1805'),
            array('c1805', 'C1800-C1805'),
        );
    }
}
