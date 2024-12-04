<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Periodical;
use App\Entity\Compilation;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Meilisearch\Bundle\SearchService;
use Meilisearch\Client;
use Meilisearch\Contracts\MultiSearchFederation;
use App\Services\MeilisearchHelper;
use Meilisearch\Contracts\SearchQuery;

class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'homepage')]
    #[Template]
    public function index() : array {
        return [];
    }

    #[Route(path: '/search', name: 'search')]
    #[Template]
    public function search(Client $client, SearchService $searchService, Request $request) : array {
        $config = $searchService->getConfiguration();
        $searchFilters = [];
        $queries = [];
        $facetsByIndex = [];

        $sortOptions = MeilisearchHelper::getDefaultSortOptions();
        $filters = $request->query->all('filters');
        if (array_key_exists('recordType', $filters)) {
            $searchFilters[] = MeilisearchHelper::generateTermFilter('recordType', $filters['recordType']);
        }

        $indexes = ['book', 'person', 'periodical', 'alias', 'place', 'publisher', 'compilation'];
        foreach ($indexes as $index) {
            $queries[] = (new SearchQuery())
                ->setIndexUid($config['prefix'] . $index)
                ->setQuery($request->query->get('q') ?? '*')
                // Highlights
                ->setAttributesToHighlight(['*'])
                ->setHighlightPreTag('<span class="hl">')
                ->setHighlightPostTag('</span>')
                // Sorting
                ->setSort(MeilisearchHelper::getSort($sortOptions, $request->query->getString('order', '')) ?? [])
                // Filtering
                ->setFilter($searchFilters)
                // Scoring
                // ->setRankingScoreThreshold(0.2)
                ->setShowRankingScore(true);
            $facetsByIndex[$config['prefix'] . $index] = ['recordType'];
        }
        $multiSearchFederation = new MultiSearchFederation();
        // Pagination is handled by paginator service (not ideal but easier)
        $multiSearchFederation->setLimit(10000);
        // Facets
        $multiSearchFederation->setFacetsByIndex($facetsByIndex);
        $multiSearchFederation->setMergeFacets([ 'maxValuesPerFacet' => 10000 ]);

        // Run search
        $searchResults = $client->multiSearch($queries, $multiSearchFederation);
        $results = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));
        // fix sort order for record type
        arsort($searchResults['facetDistribution']['recordType'], SORT_NUMERIC);
        return [
            'results' => $results,
            'searchResults' => $searchResults,
            'facetDistribution' => $searchResults['facetDistribution'],
            'sortOptions' => $sortOptions,
        ];
    }

    #[Route(path: '/search_title', name: 'search_title')]
    #[Template]
    public function searchTitle(Client $client, SearchService $searchService, Request $request) : array {
        $config = $searchService->getConfiguration();
        $searchFilters = [];
        $queries = [];
        $facetsByIndex = [];

        $sortOptions = MeilisearchHelper::getDefaultSortOptions();
        $rangeFilters = [
            'dateYear' => MeilisearchHelper::rangeFilter(1750, (int) date('Y'), 50),
        ];

        $filters = $request->query->all('filters');
        if (array_key_exists('dateYear', $filters)) {
            $searchFilters[] = MeilisearchHelper::generateRangeFilter('dateYear', $rangeFilters['dateYear'], $filters['dateYear']);
        }
        if (array_key_exists('location', $filters)) {
            $searchFilters[] = MeilisearchHelper::generateTermFilter('location', $filters['location']);
        }
        if (array_key_exists('recordType', $filters)) {
            $searchFilters[] = MeilisearchHelper::generateTermFilter('recordType', $filters['recordType']);
        }

        $indexes = ['book', 'periodical', 'compilation'];
        foreach ($indexes as $index) {
            $queries[] = (new SearchQuery())
                ->setIndexUid($config['prefix'] . $index)
                ->setQuery($request->query->get('q') ?? '*')
                // Highlights
                ->setAttributesToHighlight(['*'])
                ->setHighlightPreTag('<span class="hl">')
                ->setHighlightPostTag('</span>')
                // Sorting
                ->setSort(MeilisearchHelper::getSort($sortOptions, $request->query->getString('order', '')) ?? [])
                // Filtering
                ->setFilter($searchFilters)
                // Scoring
                // ->setRankingScoreThreshold(0.2)
                ->setShowRankingScore(true);
            $facetsByIndex[$config['prefix'] . $index] = ['*'];
        }
        $multiSearchFederation = new MultiSearchFederation();
        // Pagination is handled by paginator service (not ideal but easier)
        $multiSearchFederation->setLimit(10000);
        // Facets
        $multiSearchFederation->setFacetsByIndex($facetsByIndex);
        $multiSearchFederation->setMergeFacets([ 'maxValuesPerFacet' => 10000 ]);

        // Run search
        $searchResults = $client->multiSearch($queries, $multiSearchFederation);
        $results = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));
        $searchResults['facetDistribution']['dateYear'] = MeilisearchHelper::addRangeFilterCounts($rangeFilters['dateYear'], $searchResults['facetDistribution']['dateYear']);
        // fix sort order for location and record type
        arsort($searchResults['facetDistribution']['recordType'], SORT_NUMERIC);
        arsort($searchResults['facetDistribution']['location'], SORT_NUMERIC);
        return [
            'results' => $results,
            'searchResults' => $searchResults,
            'facetDistribution' => $searchResults['facetDistribution'],
            'sortOptions' => $sortOptions,
        ];
    }

    #[Route(path: '/privacy', name: 'privacy')]
    #[Template]
    public function privacy() : void {}
}
