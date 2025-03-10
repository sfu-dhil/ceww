<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Repository\PersonRepository;
use App\Repository\PublisherRepository;
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

#[Route(path: '/publisher')]
class PublisherController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'publisher_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Publisher::class, 'e')->orderBy('e.name', 'ASC');
        $query = $qb->getQuery();

        $publishers = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);

        return [
            'publishers' => $publishers,
        ];
    }

    #[Route(path: '/typeahead', name: 'publisher_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, PublisherRepository $repo) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/search', name: 'publisher_search')]
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
        if (array_key_exists('places', $filters)) {
            $searchParams['filter'][] = MeilisearchHelper::generateTermFilter('places', $filters['places']);
        }

        // Run search
        $searchResults = $searchService->rawSearch(Publisher::class, $request->query->get('q') ?? '*', $searchParams);
        $results = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'results' => $results,
            'facetDistribution' => $searchResults['facetDistribution'],
            'sortOptions' => $sortOptions,
        ];
    }

    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Route(path: '/new', name: 'publisher_new', methods: ['GET', 'POST'])]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($publisher);
            $em->flush();

            $this->addFlash('success', 'The new publisher was created.');

            return $this->redirectToRoute('publisher_show', ['id' => $publisher->getId()]);
        }

        return [
            'publisher' => $publisher,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'publisher_show', methods: ['GET'])]
    #[Template]
    public function show(Publisher $publisher, PersonRepository $repo) : array {
        return [
            'publisher' => $publisher,
            'people' => $repo->byPublisher($publisher),
        ];
    }

    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Route(path: '/{id}/edit', name: 'publisher_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Publisher $publisher) : array|RedirectResponse {
        $editForm = $this->createForm(PublisherType::class, $publisher);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The publisher has been updated.');

            return $this->redirectToRoute('publisher_show', ['id' => $publisher->getId()]);
        }

        return [
            'publisher' => $publisher,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    #[Route(path: '/{id}/merge', name: 'publisher_merge')]
    #[Template]
    public function merge(Request $request, Publisher $publisher, Merger $merger, PublisherRepository $repo) : array|RedirectResponse {
        if ('POST' === $request->getMethod()) {
            $publishers = $repo->findBy(['id' => $request->request->all('publishers')]);
            $count = count($publishers);
            $merger->publishers($publisher, $publishers);
            $this->addFlash('success', "Merged {$count} publishers into {$publisher->getName()}.");

            return $this->redirect($this->generateUrl('publisher_show', ['id' => $publisher->getId()]));
        }

        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $publishers = $query->execute();
        } else {
            $publishers = [];
        }

        return [
            'publisher' => $publisher,
            'publishers' => $publishers,
            'q' => $q,
        ];
    }

    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    #[Route(path: '/{id}/delete', name: 'publisher_delete', methods: ['GET', 'POST'])]
    public function delete(EntityManagerInterface $em, Publisher $publisher) : RedirectResponse {
        $em->remove($publisher);
        $em->flush();
        $this->addFlash('success', 'The publisher was deleted.');

        return $this->redirectToRoute('publisher_index');
    }
}
