<?php

declare(strict_types=1);

// see: https://github.com/FriendsOfSymfony/FOSElasticaBundle/issues/1521

namespace App\Services;

use Elastica\Exception\InvalidException;
use Elastica\Index;
use Elastica\ResultSet\BuilderInterface;
use Elastica\Search;

class MultiIndex extends Index {
    protected array $_indices = [];

    public function addIndex(Index|string $index) : self {
        if ($index instanceof Index) {
            $index = $index->getName();
        }

        if ( ! is_scalar($index)) {
            throw new InvalidException('Invalid param type');
        }

        $this->_indices[] = (string) $index;

        return $this;
    }

    public function addIndices(array $indices = []) : self {
        foreach ($indices as $index) {
            $this->addIndex($index);
        }

        return $this;
    }

    public function getIndices() : array {
        return $this->_indices;
    }

    public function createSearch($query = '', $options = null, ?BuilderInterface $builder = null) : Search {
        $search = new Search($this->getClient(), $builder);
        // $search->addIndex($this);
        $search->addIndices($this->getIndices());
        $search->setOptionsAndQuery($options, $query);

        return $search;
    }
}
