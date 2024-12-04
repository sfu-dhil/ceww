<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use App\Services\Merger;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Meilisearch\Bundle\SearchService;
use App\Services\MeilisearchHelper;

#[Route(path: '/place')]
class PlaceController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'place_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Place::class, 'e')->orderBy('e.sortableName', 'ASC');
        $query = $qb->getQuery();

        $places = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'places' => $places,
        ];
    }

    #[Route(path: '/typeahead', name: 'place_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, PlaceRepository $repo) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => $result->getName(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/search', name: 'place_search')]
    #[Template]
    public function search(SearchService $searchService, Request $request) : array {
        $sortOptions = MeilisearchHelper::getDefaultSortOptions();
        $searchParams = [
            // Pagination is handled by paginator service (not ideal but easier)
            'limit' => 10000,
            // Highlights
            'attributesToHighlight' => ["*"],
            'highlightPreTag' => '<span class="hl">',
            'highlightPostTag' => '</span>',
            // Sorting
            'sort' => MeilisearchHelper::getSort($sortOptions, $request->query->getString('order', '')),
            // Filtering
            'filter' => [],
            // Scoring
            // 'rankingScoreThreshold' => 0.2,
            'showRankingScore' => true,
            // Facets
            'facets' => ['*'],
        ];
        $filters = $request->query->all('filters');
        if (array_key_exists('country', $filters)) {
            $searchParams['filter'][] = MeilisearchHelper::generateTermFilter('country', $filters['country']);
        }
        if (array_key_exists('region', $filters)) {
            $searchParams['filter'][] = MeilisearchHelper::generateTermFilter('region', $filters['region']);
        }

        // Run search
        $searchResults = $searchService->rawSearch(Place::class, $request->query->get('q') ?? '*', $searchParams);
        $results = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'results' => $results,
            'facetDistribution' => $searchResults['facetDistribution'],
            'sortOptions' => $sortOptions,
        ];
    }

    #[Route(path: '/new', name: 'place_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($place);
            $em->flush();

            $this->addFlash('success', 'The new place was created.');

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return [
            'place' => $place,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'place_show', methods: ['GET'])]
    #[Template]
    public function show(SearchService $searchService, Request $request, PlaceRepository $placeRepository, Place $place) : array {
        $nearbyResults = null;

        if ($place->getCoordinates()) {
            $geoPoint = $place->getCoordinates();
            $searchParams = [
                // Pagination is handled by paginator service (not ideal but easier)
                'limit' => 10000,
                // Sorting
                'sort' =>  ["_geoPoint({$geoPoint}):asc"],
                // Filtering
                'filter' => ["objectID != {$place->getId()}", "_geoRadius({$geoPoint}, 50000)"],
            ];
            // Run search
            $searchResults = $searchService->rawSearch(Place::class, '*', $searchParams);
            $nearbyResults = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));
        }

        return [
            'place' => $place,
            'nearbyResults' => $nearbyResults,
            'next' => $placeRepository->next($place),
            'previous' => $placeRepository->previous($place),
        ];
    }

    #[Route(path: '/{id}/edit', name: 'place_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Place $place) : array|RedirectResponse {
        $editForm = $this->createForm(PlaceType::class, $place);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The place has been updated.');

            return $this->redirectToRoute('place_show', ['id' => $place->getId()]);
        }

        return [
            'place' => $place,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Route(path: '/{id}/merge', name: 'place_merge')]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    #[Template]
    public function merge(Request $request, Place $place, Merger $merger, PlaceRepository $repo) : array|RedirectResponse {
        if ('POST' === $request->getMethod()) {
            $places = $repo->findBy(['id' => $request->request->all('places')]);
            $count = count($places);
            $merger->places($place, $places);
            $this->addFlash('success', "Merged {$count} places into {$place->getName()}.");

            return $this->redirect($this->generateUrl('place_show', ['id' => $place->getId()]));
        }

        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $places = $query->execute();
        } else {
            $places = [];
        }

        return [
            'place' => $place,
            'places' => $places,
            'q' => $q,
        ];
    }

    #[Route(path: '/{id}/delete', name: 'place_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Place $place) : RedirectResponse {
        if ( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em->remove($place);
        $em->flush();
        $this->addFlash('success', 'The place was deleted.');

        return $this->redirectToRoute('place_index');
    }
}
