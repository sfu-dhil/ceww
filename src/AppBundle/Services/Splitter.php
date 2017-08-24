<?php

namespace AppBundle\Services;

use Exception;
use Monolog\Logger;
use Normalizer;

class Splitter {

    /**
     * @var Logger
     */
    private $logger;
    private $delimiters = [
        '(' => ')',
        '[' => ']',
    ];

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    public function trim($s) {
        return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $s);
    }

    public function split($string, $delim = ';') {
        $s = Normalizer::normalize($string);
        $i = 0;
        $value = '';
        $list = [];
        $find = null;
        $len = mb_strlen($s);
        while ($i < $len) {
            $c = mb_substr($s, $i, 1);
            if (isset($this->delimiters[$c])) {
                if ($find !== null) {
                    throw new Exception('Nested delimiters are unsupported');
                }
                $find = $this->delimiters[$c];
                $value .= $c;
            } elseif ($c === $find) {
                $find = null;
                $value .= $c;
            } elseif ($c === $delim && $find === null) {
                $list[] = $value;
                $value = '';
            } else {
                $value .= $c;
            }
            $i++;
        }
        if ($value) {
            $list[] = $value;
        }
        return array_filter(array_map([$this, 'trim'], $list));
    }

}
