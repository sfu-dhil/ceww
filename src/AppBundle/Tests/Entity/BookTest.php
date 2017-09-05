<?php


namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Book;
use AppBundle\Entity\Publication;
use PHPUnit_Framework_TestCase;

class BookTest extends PHPUnit_Framework_TestCase {

    public function testGetCategory() {
        $book = new Book();        
        $this->assertEquals(Publication::BOOK, $book->getCategory());
    }

}    