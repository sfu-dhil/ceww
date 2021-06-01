<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\SolrBundle\Annotation as Solr;

/**
 * Book.
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @Solr\Document(
 *     @Solr\CopyField(from={"title", "description", "dateYear", "location", "genres", "contributions", "publishers"}, to="content", type="texts")
 * )
 */
class Book extends Publication {
    public function getCategory() {
        return self::BOOK;
    }
}
