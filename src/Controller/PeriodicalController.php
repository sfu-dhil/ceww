<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Contribution;
use App\Entity\Periodical;
use App\Entity\Publication;
use App\Form\ContributionType;
use App\Form\PeriodicalType;
use App\Repository\PeriodicalRepository;
use App\Repository\PublicationRepository;
use App\Services\Merger;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Periodical controller.
 *
 * @Route("/periodical")
 */
class PeriodicalController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Periodical entities.
     *
     * @Route("/", name="periodical_index", methods={"GET"})
     *
     * @Template
     *
     * @param PublicationRepository $repo
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request, PeriodicalRepository $repo) {
        $em = $this->getDoctrine()->getManager();
        $pageSize = $this->getParameter('page_size');

        if ($request->query->has('alpha')) {
            $page = $repo->letterPage($request->query->get('alpha'), Publication::PERIODICAL, $pageSize);

            return $this->redirectToRoute('periodical_index', ['page' => $page]);
        }
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Periodical::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();

        $periodicals = $this->paginator->paginate($query, $request->query->getint('page', 1), $pageSize);
        $letterIndex = [];

        foreach ($periodicals as $periodical) {
            $title = $periodical->getSortableTitle();
            if ( ! $title) {
                continue;
            }
            $letterIndex[mb_convert_case($title[0], MB_CASE_UPPER)] = 1;
        }

        return [
            'periodicals' => $periodicals,
            'activeLetters' => array_keys($letterIndex),
        ];
    }

    /**
     * Creates a new Periodical entity.
     *
     * @Route("/new", name="periodical_new", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function newAction(Request $request) {
        $periodical = new Periodical();
        $form = $this->createForm(PeriodicalType::class, $periodical);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($periodical->getContributions() as $contribution) {
                $contribution->setPublication($periodical);
            }
            $em = $this->getDoctrine()->getManager();
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

    /**
     * Finds and displays a Periodical entity.
     *
     * @Route("/{id}", name="periodical_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Periodical $periodical) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Periodical::class);

        return [
            'periodical' => $periodical,
            'next' => $repo->next($periodical),
            'previous' => $repo->previous($periodical),
        ];
    }

    /**
     * Displays a form to edit an existing Periodical entity.
     *
     * @Route("/{id}/edit", name="periodical_edit", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function editAction(Request $request, Periodical $periodical) {
        $editForm = $this->createForm(PeriodicalType::class, $periodical);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($periodical->getContributions() as $contribution) {
                $contribution->setPublication($periodical);
            }

            $em = $this->getDoctrine()->getManager();
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
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/merge", name="periodical_merge")
     *
     * @Template
     */
    public function mergeAction(Request $request, Periodical $periodical, EntityManagerInterface $em, Merger $merger, PeriodicalRepository $repo) {
        if ('POST' === $request->getMethod()) {
            $periodicals = $repo->findBy(['id' => $request->request->get('periodicals')]);
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

    /**
     * Deletes a Periodical entity.
     *
     * @Route("/{id}/delete", name="periodical_delete", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteAction(Request $request, Periodical $periodical) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($periodical);
        $em->flush();
        $this->addFlash('success', 'The periodical was deleted.');

        return $this->redirectToRoute('periodical_index');
    }

    /**
     * Creates a new Periodical contribution entity.
     *
     * @Route("/{id}/contributions/new", name="periodical_new_contribution")
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function newContribution(Request $request, Periodical $periodical) {
        $contribution = new Contribution();

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contribution->setPublication($periodical);
            $em = $this->getDoctrine()->getManager();
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

    /**
     * Show periodical contributions list with edit/delete action items.
     *
     * @Route("/{id}/contributions", name="periodical_show_contributions")
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function showContributions(Periodical $periodical) {
        return [
            'periodical' => $periodical,
        ];
    }

    /**
     * Displays a form to edit an existing periodical Contribution entity.
     *
     * @Route("/contributions/{id}/edit", name="periodical_edit_contributions")
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function editContribution(Request $request, Contribution $contribution) {
        $editForm = $this->createForm(ContributionType::class, $contribution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The contribution has been updated.');

            return $this->redirectToRoute('periodical_show_contributions', ['id' => $contribution->getPublicationId()]);
        }

        return [
            'contribution' => $contribution,
            'edit_form' => $editForm->CreateView(),
        ];
    }

    /**
     * Deletes a periodical Contribution entity.
     *
     * @Route("/contributions/{id}/delete", name="periodical_delete_contributions")
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteContribution(Request $request, Contribution $contribution) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contribution);
        $em->flush();
        $this->addFlash('success', 'The contribution was deleted.');

        return $this->redirectToRoute('periodical_show_contributions', ['id' => $contribution->getPublicationId()]);
    }
}
