<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\SolrBundle\Annotation as Solr;

/**
 * Collection.
 *
 * @ORM\Table(name="collection")
 * @ORM\Entity(repositoryClass="App\Repository\CompilationRepository")
 *
 * @Solr\Document(
 *     @Solr\CopyField(from={"title", "description", "dateYear", "location", "genres", "contributions", "publishers"}, to="content", type="texts"),
 *     @Solr\CopyField(from={"genres"}, to="genres_fct", type="strings"),
 *     @Solr\CopyField(from={"location"}, to="location_fct", type="string")
 * )
 */
class Compilation extends Publication {
    public function getCategory() {
        return self::COMPILATION;
    }
}
