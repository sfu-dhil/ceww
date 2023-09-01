<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Periodical;
use App\Entity\Publication;
use App\Form\ContributionType;
use App\Form\PeriodicalType;
use App\Repository\PeriodicalRepository;
use App\Services\Merger;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Periodical controller.
 */
#[Route(path: '/periodical')]
class PeriodicalController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'periodical_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request, PeriodicalRepository $repo) : array|RedirectResponse {
        $pageSize = $this->getParameter('page_size');

        if ($request->query->has('alpha')) {
            $page = $repo->letterPage($request->query->get('alpha'), Publication::PERIODICAL, $pageSize);

            return $this->redirectToRoute('periodical_index', ['page' => $page]);
        }
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Periodical::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();

        $periodicals = $this->paginator->paginate($query, $request->query->getInt('page', 1), $pageSize);
        $letterIndex = [];

        foreach ($periodicals as $periodical) {
            $title = $periodical->getSortableTitle();
            if ( ! $title) {
                continue;
            }
            $letterIndex[mb_convert_case((string) $title[0], MB_CASE_UPPER)] = 1;
        }

        return [
            'periodicals' => $periodicals,
            'activeLetters' => array_keys($letterIndex),
        ];
    }

    #[Route(path: '/new', name: 'periodical_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $periodical = new Periodical();
        $form = $this->createForm(PeriodicalType::class, $periodical);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($periodical->getContributions() as $contribution) {
                $contribution->setPublication($periodical);
            }
            $em->persist($periodical);
            $em->flush();

            $this->addFlash('success', 'The new periodical was created.');

            return $this->redirectToRoute('periodical_show', ['id' => $periodical->getId()]);
        }

        return [
            'periodical' => $periodical,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'periodical_show', methods: ['GET'])]
    #[Template]
    public function show(PeriodicalRepository $periodicalRepository, Periodical $periodical) : array {
        return [
            'periodical' => $periodical,
            'next' => $periodicalRepository->next($periodical),
            'previous' => $periodicalRepository->previous($periodical),
        ];
    }

    #[Route(path: '/{id}/edit', name: 'periodical_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Periodical $periodical) : array|RedirectResponse {
        $editForm = $this->createForm(PeriodicalType::class, $periodical);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($periodical->getContributions() as $contribution) {
                $contribution->setPublication($periodical);
            }
            $em->flush();
            $this->addFlash('success', 'The periodical has been updated.');

            return $this->redirectToRoute('periodical_show', ['id' => $periodical->getId()]);
        }

        return [
            'periodical' => $periodical,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Finds and merges periodicals.
     */
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    #[Route(path: '/{id}/merge', name: 'periodical_merge')]
    #[Template]
    public function merge(Request $request, Periodical $periodical, EntityManagerInterface $em, Merger $merger, PeriodicalRepository $repo) : null|array|RedirectResponse {
        if ('POST' === $request->getMethod()) {
            $periodicals = $repo->findBy(['id' => $request->request->all('periodicals')]);
            $count = count($periodicals);
            $merger->periodicals($periodical, $periodicals);
            $this->addFlash('success', "Merged {$count} periodicals into {$periodical->getTitle()}.");

            return $this->redirect($this->generateUrl('periodical_show', ['id' => $periodical->getId()]));
        }

        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $periodicals = $query->execute();
        } else {
            $periodicals = [];
        }

        return [
            'periodical' => $periodical,
            'periodicals' => $periodicals,
            'q' => $q,
        ];
    }

    #[Route(path: '/{id}/delete', name: 'periodical_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Periodical $periodical) : RedirectResponse {
        $em->remove($periodical);
        $em->flush();
        $this->addFlash('success', 'The periodical was deleted.');

        return $this->redirectToRoute('periodical_index');
    }

    #[Route(path: '/{id}/contributions/new', name: 'periodical_new_contribution')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function newContribution(EntityManagerInterface $em, Request $request, Periodical $periodical) : array|RedirectResponse {
        $contribution = new Contribution();

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contribution->setPublication($periodical);
            $em->persist($contribution);
            $em->flush();

            $this->addFlash('success', 'The new contribution was created.');

            return $this->redirectToRoute('periodical_show_contributions', ['id' => $periodical->getId()]);
        }

        return [
            'periodical' => $periodical,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}/contributions', name: 'periodical_show_contributions')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function showContributions(Periodical $periodical) : array {
        return [
            'periodical' => $periodical,
        ];
    }

    #[Route(path: '/contributions/{id}/edit', name: 'periodical_edit_contributions')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function editContribution(EntityManagerInterface $em, Request $request, Contribution $contribution) : array|RedirectResponse {
        $editForm = $this->createForm(ContributionType::class, $contribution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The contribution has been updated.');

            return $this->redirectToRoute('periodical_show_contributions', ['id' => $contribution->getPublicationId()]);
        }

        return [
            'contribution' => $contribution,
            'edit_form' => $editForm->CreateView(),
        ];
    }

    #[Route(path: '/contributions/{id}/delete', name: 'periodical_delete_contributions')]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function deleteContribution(EntityManagerInterface $em, Contribution $contribution) : RedirectResponse {
        $em->remove($contribution);
        $em->flush();
        $this->addFlash('success', 'The contribution was deleted.');

        return $this->redirectToRoute('periodical_show_contributions', ['id' => $contribution->getPublicationId()]);
    }
}
