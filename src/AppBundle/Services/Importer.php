<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

/**
 * Description of Importer
 *
 * @author mjoyce
 */
class Importer {
    /**
     * ORM entity manager.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * Service logger.
     *
     * @var Logger
     */
    private $logger;

    /**
     * Set the service logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the ORM.
     *
     * @param Registry $registry
     */
    public function setDoctrine(Registry $registry)
    {
        $this->em = $registry->getManager();
    }
    
    public function processDate($str) {
        if (!$str || ctype_space($str)) {
            return null;
        }
        $matches = array();
        if (preg_match('/(\d{4})-(\d{4})/', $str, $matches)) {
            return array($matches[1], $matches[2]);
        }
        if (preg_match('/^(\d{2})-([a-zA-Z]{3})-(\d{2})$/', $str, $matches)) {
            return $matches[3] + 1900;
        }
        if (preg_match('/^([a-zA-Z]{3})-(\d{2})$/', $str, $matches)) {
            return $matches[2] + 1900;
        }
        if (preg_match('/(\d{4})/', $str, $matches)) {
            return $matches[1];
        }
        $this->logger->warning("Unparseable date: {$str}");
        return null;
    }
    
    public function split($s, $delim = ';', $alternate = null) {
        if ($alternate && substr_count($s, $alternate) > 1 && substr_count($delim, $s) < substr_count($s, $alternate)) {
            $this->logger->warning('Possibly malformed string: ' . $s);
            $a = explode($alternate, $s);
        } else {
            $a = explode($delim, $s);
        }
        for ($i = 0; $i < count($a); $i++) {
            $a[$i] = trim($a[$i]);
        }
        return $a;
    }

    public function cleanPlaceName($placeName) {
        $filters = array(
            '/^"[^"]*"\s*/' => '', // remove quoted place name at start
            '/\s+\([^)]*\)$/' => '', // remove parenthesized location
            '/^\s*near\b\s*/i' => '', // remove "near "
        );
        
        $name = $placeName;
        foreach($filters as $key => $value) {
            $name = preg_replace($key, $value, $name);
        }
        return trim($name);
    }
    
    public function titleCase($title) {
        $cased = ucwords(strtolower(trim($title)));
        // this is a terrible hack.
        if($cased[0] === '"') {
            // embarassing really.
            $cased[1] = strtoupper($cased[1]);
        }
        return $cased;
    }
    
    public function cleanTitle($publicationTitle) {
        $filters = array(
            '/\(c?\d{4}(-c?\d{4})?\)/' => '', // remove year or range
            '/^"([^"]*)"$/' => '$1', // remove front/rear quotes.
        );
        
        $title = $publicationTitle;
        foreach($filters as $key => $value) {
            $title = preg_replace($key, $value, $title);
        }
        return $this->titleCase($title);
    }
    
    public function sortableTitle($cleanTitle) {
        $filters = array(
            '/^(the|an?)\b\s*(.*)/i' => '$2, $1', // move The, A, An to end.
            '/^[^[:word:][:space:]]+/' => '', // remove non-word chars at start.
        );
        
        $title = strtolower($cleanTitle);
        foreach($filters as $key => $value) {
            $title = preg_replace($key, $value, $title);
        }
        return trim($title);
    }

}
