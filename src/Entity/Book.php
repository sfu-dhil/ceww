<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Book.
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book extends Publication {
    public function getCategory() {
        return self::BOOK;
    }
}
