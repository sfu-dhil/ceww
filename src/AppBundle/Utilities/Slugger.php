<?php

namespace AppBundle\Utilities;

/**
 * Description of Slugger
 *
 * @author mjoyce
 */
class Slugger {
    
    /**
     * Create a URL-friendly slug from a string. 
     * 
     * Drops leading/trailing spaces, transliterates digraphs, lowercases, 
     * and replaces non letter/digit characters to the separator. Periods at 
     * the end of the string are removed.
     * 
     * @param string $string
     * @param string  $separator
     * @return string
     */
    public function slug($string, $separator = '-') {
        // trim spaces.
        $s = preg_replace('/(^[\s.]*)|([\s.]*$)/u', '', $string);
        
        // transliterate digraphs
        $s = iconv('utf-8', 'us-ascii//TRANSLIT', $s);
        
        // lowercase
        $s = mb_convert_case($s, MB_CASE_LOWER, 'UTF-8');
        
        // trailing periods.
        $s = preg_replace('/\.+$/u', '', $s);
        
        // strip non letter/digit/period/space chars
        $s = preg_replace('/[^-_a-z0-9. ]/u', '', $s);
        
        // transform spaces and runs of separators to separator.
        $quoted = preg_quote($separator, '/');
        $s = preg_replace("/(\s|$quoted)+/u", $separator, $s);  
        
        return $s;
    }
    
}
