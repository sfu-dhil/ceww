<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use App\Repository\PublisherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Meilisearch\Bundle\SearchService;
use App\Services\MeilisearchHelper;

#[Route(path: '/person')]
class PersonController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'person_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Person::class, 'e');
        if ( ! $this->isGranted('ROLE_USER')) {
            $qb->where("e.gender <> 'm'")
                ->andWhere('e.canadian is null OR e.canadian != 0')
            ;
        }
        $qb->orderBy('e.sortableName');
        $query = $qb->getQuery();

        $people = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'people' => $people,
        ];
    }

    #[Route(path: '/pageinfo', name: 'person_pageinfo')]
    public function pageInfo(Request $request, PersonRepository $repo) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $people = $repo->pageInfoQuery($q, $this->getParameter('page_size')); // should be first person on page, last person on page.
        $data = [
            'first' => ($people['first'] ? [
                'name' => $people['first']->getFullname(),
                'id' => $people['first']->getId(),
            ] : null),
            'last' => ($people['last'] ? [
                'name' => $people['last']->getFullname(),
                'id' => $people['last']->getId(),
            ] : null),
            'pages' => ceil($people['total'] / $this->getParameter('page_size')),
        ];

        return new JsonResponse($data);
    }

    #[Route(path: '/typeahead', name: 'person_typeahead', methods: ['GET'])]
    public function typeahead(PersonRepository $personRepository, Request $request) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($personRepository->typeaheadQuery($q) as $result) {
            $name = $result->getFullname();
            if (is_countable($result->getAliases()) ? count($result->getAliases()) : 0) {
                $name .= ' aka ' . $result->getAliases()->first();
            }
            $data[] = [
                'id' => $result->getId(),
                'text' => $name,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/search', name: 'person_search')]
    #[Template]
    public function search(SearchService $searchService, Request $request) : array {
        $sortOptions = MeilisearchHelper::getDefaultSortOptions();
        $rangeFilters = [
            'birthDate' => MeilisearchHelper::rangeFilter(1750, (int) date('Y'), 50),
            'deathDate' => MeilisearchHelper::rangeFilter(1750, (int) date('Y'), 50),
        ];
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
        if (array_key_exists('birthDate', $filters)) {
            $searchParams['filter'][] = MeilisearchHelper::generateRangeFilter('birthDate', $rangeFilters['birthDate'], $filters['birthDate']);
        }
        if (array_key_exists('deathDate', $filters)) {
            $searchParams['filter'][] = MeilisearchHelper::generateRangeFilter('deathDate', $rangeFilters['deathDate'], $filters['deathDate']);
        }

        // Run search
        $searchResults = $searchService->rawSearch(Person::class, $request->query->get('q') ?? '*', $searchParams);
        $results = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));

        $searchResults['facetDistribution']['birthDate'] = MeilisearchHelper::addRangeFilterCounts($rangeFilters['birthDate'], $searchResults['facetDistribution']['birthDate']);
        $searchResults['facetDistribution']['deathDate'] = MeilisearchHelper::addRangeFilterCounts($rangeFilters['deathDate'], $searchResults['facetDistribution']['deathDate']);

        return [
            'results' => $results,
            'facetDistribution' => $searchResults['facetDistribution'],
            'sortOptions' => $sortOptions,
        ];
    }

    #[Route(path: '/new', name: 'person_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($person);
            $em->flush();

            $this->addFlash('success', 'The new person was created.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'person_show', methods: ['GET'])]
    #[Template]
    public function show(Person $person, PersonRepository $repo, PublisherRepository $publisherRepo) : array|RedirectResponse {
        if ( ! $this->isGranted('ROLE_USER') && Person::FEMALE !== $person->getGender()) {
            throw new NotFoundHttpException('Cannot find that person.');
        }

        return [
            'person' => $person,
            'next' => $repo->next($person),
            'previous' => $repo->previous($person),
            'publishers' => $publisherRepo->byPerson($person),
        ];
    }

    #[Route(path: '/{id}/edit', name: 'person_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Person $person) : array|RedirectResponse {
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The person has been updated.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Route(path: '/{id}/delete', name: 'person_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Person $person) : RedirectResponse {
        $em->remove($person);
        $em->flush();
        $this->addFlash('success', 'The person was deleted.');

        return $this->redirectToRoute('person_index');
    }
}
