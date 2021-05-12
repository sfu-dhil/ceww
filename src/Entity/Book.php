<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

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
