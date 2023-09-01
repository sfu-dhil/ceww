<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\ElasticaToModelTransformerCollection;
use App\Services\ElasticSearchHelper;
use App\Services\MultiIndex;
use Elastica\Index;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use FOS\ElasticaBundle\Index\IndexManager;
use FOS\ElasticaBundle\Transformer\ElasticaToModelTransformerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    public function __construct(
        private IndexManager $indexManager,
        private ElasticaToModelTransformerInterface $aliasTransformer,
        private ElasticaToModelTransformerInterface $bookTransformer,
        private ElasticaToModelTransformerInterface $compilationTransformer,
        private ElasticaToModelTransformerInterface $periodicalTransformer,
        private ElasticaToModelTransformerInterface $personTransformer,
        private ElasticaToModelTransformerInterface $placeTransformer,
        private ElasticaToModelTransformerInterface $publisherTransformer,
    ) {}

    #[Route(path: '/', name: 'homepage')]
    #[Template]
    public function index() : array {
        return [];
    }

    #[Route(path: '/search', name: 'search')]
    #[Template]
    public function search(Request $request) : array {
        // setup multi index finder
        $indices = [
            'alias' => $this->indexManager->getIndex('alias'),
            'book' => $this->indexManager->getIndex('book'),
            'compilation' => $this->indexManager->getIndex('compilation'),
            'periodical' => $this->indexManager->getIndex('periodical'),
            'person' => $this->indexManager->getIndex('person'),
            'place' => $this->indexManager->getIndex('place'),
            'publisher' => $this->indexManager->getIndex('publisher'),
        ];
        $searchable = new MultiIndex($indices['alias']->getClient(), $indices['alias']->getName());
        $searchable->addIndices(array_values($indices));

        $transformer = new ElasticaToModelTransformerCollection([
            $indices['alias']->getName() => $this->aliasTransformer,
            $indices['book']->getName() => $this->bookTransformer,
            $indices['compilation']->getName() => $this->compilationTransformer,
            $indices['periodical']->getName() => $this->periodicalTransformer,
            $indices['person']->getName() => $this->personTransformer,
            $indices['place']->getName() => $this->placeTransformer,
            $indices['publisher']->getName() => $this->publisherTransformer,
        ]);
        $finder = new TransformedFinder($searchable, $transformer);

        // setup search query
        $elasticSearchHelper = new ElasticSearchHelper([
            'queryTermFields' => [
                'name^2.5',
                'fullName^2.5',
                'title^2.5',
                'description^0.5',
                'country^0.2',
                'region^0.5',
                'continuedFrom^0.6',
                'continuedBy^0.6',

                'genres.label^0.5',
                'aliases.name^1.3',
                'places.name^0.6',
                'people.fullName^1.3',
                'contributions.person.fullName^0.5',
                'location.name^0.5',
                'birthPlace.name^0.4',
                'deathPlace.name^0.4',
                'residences.name^0.3',
            ],
            'filters' => [
                'type' => ElasticSearchHelper::generateTermFilter('_index'),
            ],
            'sort' => ElasticSearchHelper::generateDefaultSortOrder(),
            'highlights' => [
                'name' => new stdClass(),
                'fullName' => new stdClass(),
                'title' => new stdClass(),
                'description' => new stdClass(),
                'country' => new stdClass(),
                'region' => new stdClass(),
                'continuedFrom' => new stdClass(),
                'continuedBy' => new stdClass(),

                'genres.label' => new stdClass(),
                'aliases.name' => new stdClass(),
                'places.name' => new stdClass(),
                'people.fullName' => new stdClass(),
                'contributions.person.fullName' => new stdClass(),
                'location.name' => new stdClass(),
                'birthPlace.name' => new stdClass(),
                'deathPlace.name' => new stdClass(),
                'residences.name' => new stdClass(),
            ],
        ]);
        $query = $elasticSearchHelper->getElasticQuery(
            $request->query->get('q'),
            $request->query->get('order'),
            $request->query->all('filters')
        );
        $results = $finder->createHybridPaginatorAdapter($query);

        return [
            'results' => $this->paginator->paginate($results, $request->query->getInt('page', 1), $this->getParameter('page_size')),
            'sortOptions' => ElasticSearchHelper::generateDefaultSortOrder(),
        ];

        // $qb->setHighlightFields([
        // 'continuedFrom', 'continuedBy']);
    }

    #[Route(path: '/search_title', name: 'search_title')]
    #[Template]
    public function searchTitle(Request $request) : array {
        // setup multi index finder
        $indices = [
            'book' => $this->indexManager->getIndex('book'),
            'compilation' => $this->indexManager->getIndex('compilation'),
            'periodical' => $this->indexManager->getIndex('periodical'),
        ];
        $searchable = new MultiIndex($indices['book']->getClient(), $indices['book']->getName());
        $searchable->addIndices(array_values($indices));

        $transformer = new ElasticaToModelTransformerCollection([
            $indices['book']->getName() => $this->bookTransformer,
            $indices['compilation']->getName() => $this->compilationTransformer,
            $indices['periodical']->getName() => $this->periodicalTransformer,
        ]);
        $finder = new TransformedFinder($searchable, $transformer);

        // setup search query
        $elasticSearchHelper = new ElasticSearchHelper([
            'queryTermFields' => [
                'title^2.5',
                'description^0.5',
                'location.name^0.5',
                'genres.label^0.5',
                'contributions.person.fullName^0.5',
                'publishers.name^0.4',
                'continuedFrom^0.6',
                'continuedBy^0.6',
            ],
            'filters' => [
                'publicationLocation' => ElasticSearchHelper::generateTermFilter('location.nameFacet'),
                'type' => ElasticSearchHelper::generateTermFilter('_index'),
            ],
            'rangeFilters' => [
                'publicationDate' => ElasticSearchHelper::generateRangeFilter('dateYear.year', 1750, (int) date('Y'), 50),
            ],
            'sort' => ElasticSearchHelper::generateDefaultSortOrder(),
            'highlights' => [
                'title' => new stdClass(),
                'description' => new stdClass(),
                'location.name' => new stdClass(),
                'genres.label' => new stdClass(),
                'contributions.person.fullName' => new stdClass(),
                'publishers.name' => new stdClass(),
                'continuedFrom' => new stdClass(),
                'continuedBy' => new stdClass(),
            ],
        ]);
        $query = $elasticSearchHelper->getElasticQuery(
            $request->query->get('q'),
            $request->query->get('order'),
            $request->query->all('filters')
        );
        $results = $finder->createHybridPaginatorAdapter($query);

        return [
            'results' => $this->paginator->paginate($results, $request->query->getInt('page', 1), $this->getParameter('page_size')),
            'sortOptions' => ElasticSearchHelper::generateDefaultSortOrder(),
        ];
    }

    #[Route(path: '/privacy', name: 'privacy')]
    #[Template]
    public function privacy() : void {}
}
