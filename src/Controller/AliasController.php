<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Alias;
use App\Form\AliasType;
use App\Repository\AliasRepository;
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

#[Route(path: '/alias')]
class AliasController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'alias_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Alias::class, 'e')->orderBy('e.sortableName', 'ASC');
        $query = $qb->getQuery();

        $aliases = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'aliases' => $aliases,
        ];
    }

    #[Route(path: '/search', name: 'alias_search', methods: ['GET'])]
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
            // scoring
            // 'rankingScoreThreshold' => 0.2,
            'showRankingScore' => true,
        ];
        $searchResults = $searchService->rawSearch(Alias::class, $request->query->get('q') ?? '*', $searchParams);
        $results = $this->paginator->paginate($searchResults['hits'], $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'results' => $results,
            'sortOptions' => $sortOptions,
        ];
    }

    #[Route(path: '/typeahead', name: 'alias_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, AliasRepository $repo) : JsonResponse {
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

    #[Route(path: '/new', name: 'alias_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $alias = new Alias();
        $form = $this->createForm(AliasType::class, $alias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($alias);
            $em->flush();

            $this->addFlash('success', 'The new alias was created.');

            return $this->redirectToRoute('alias_show', ['id' => $alias->getId()]);
        }

        return [
            'alias' => $alias,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'alias_show', methods: ['GET'])]
    #[Template]
    public function show(Alias $alias) : array {
        return [
            'alias' => $alias,
        ];
    }

    #[Route(path: '/{id}/edit', name: 'alias_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Alias $alias) : array|RedirectResponse {
        $editForm = $this->createForm(AliasType::class, $alias);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The alias has been updated.');

            return $this->redirectToRoute('alias_show', ['id' => $alias->getId()]);
        }

        return [
            'alias' => $alias,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Route(path: '/{id}/delete', name: 'alias_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Alias $alias) : RedirectResponse {
        $em->remove($alias);
        $em->flush();
        $this->addFlash('success', 'The alias was deleted.');

        return $this->redirectToRoute('alias_index');
    }
}
