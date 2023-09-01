<?php

declare(strict_types=1);

namespace App\Services;

use FOS\ElasticaBundle\HybridResult;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;

class ElasticaToModelTransformerCollection implements ElasticaToModelTransformerInterface {
    /**
     * @var ElasticaToModelTransformerInterface[]
     */
    protected array $transformers = [];

    /**
     * @param ElasticaToModelTransformerInterface[] $transformers
     */
    public function __construct(array $transformers) {
        $this->transformers = $transformers;
    }

    public function getObjectClass() : string {
        return implode(',', array_map(fn (ElasticaToModelTransformerInterface $transformer) => $transformer->getObjectClass(), $this->transformers));
    }

    public function getIdentifierField() : string {
        return array_map(fn (ElasticaToModelTransformerInterface $transformer) => $transformer->getIdentifierField(), $this->transformers)[0];
    }

    public function transform(array $elasticaObjects) {
        $sorted = [];
        foreach ($elasticaObjects as $object) {
            $sorted[$object->getIndex()][] = $object;
        }

        $transformed = [];
        foreach ($sorted as $type => $objects) {
            $transformedObjects = $this->transformers[$type]->transform($objects);
            $identifierGetter = 'get' . ucfirst($this->transformers[$type]->getIdentifierField());
            $transformed[$type] = array_combine(
                array_map(
                    fn ($o) => $o->{$identifierGetter}(),
                    $transformedObjects
                ),
                $transformedObjects
            );
        }

        $result = [];
        foreach ($elasticaObjects as $object) {
            if (array_key_exists((string) $object->getId(), $transformed[$object->getIndex()])) {
                $result[] = $transformed[$object->getIndex()][(string) $object->getId()];
            }
        }

        return $result;
    }

    public function hybridTransform(array $elasticaObjects) {
        $objects = $this->transform($elasticaObjects);

        $result = [];
        for ($i = 0, $j = count($elasticaObjects); $i < $j; $i++) {
            if ( ! isset($objects[$i])) {
                continue;
            }
            $result[] = new HybridResult($elasticaObjects[$i], $objects[$i]);
        }

        return $result;
    }
}
