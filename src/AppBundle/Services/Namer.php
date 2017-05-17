<?php

namespace AppBundle\Services;

class Namer {

    public function lastFirstToFull($string) {
        if( ! preg_match('/,\s*/u', $string)) {
            return $string;
        }
        list($family, $given) = preg_split('/,\s*/u', $string, 2);
        return "{$given} {$family}";
    }
    
    public function fullToLastFirst($string) {
        $matches = array();
        if( ! preg_match("/(.+)\s+(.+)$/", $string, $matches)) {
            return $string;
        }
        return implode(" ", [$matches[2], $matches[1]]);
    }
    
    public function sortableName($string) {
        return mb_convert_case($string, MB_CASE_LOWER, 'UTF-8');
    }
}
