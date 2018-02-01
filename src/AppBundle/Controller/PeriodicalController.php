<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Periodical;
use AppBundle\Form\PeriodicalType;

/**
 * Periodical controller.
 *
 * @Route("/periodical")
 */
class PeriodicalController extends Controller {

    /**
     * Lists all Periodical entities.
     *
     * @Route("/", name="periodical_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Periodical::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $periodicals = $paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return array(
            'periodicals' => $periodicals,
        );
    }

    /**
     * Creates a new Periodical entity.
     *
     * @Route("/new", name="periodical_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $periodical = new Periodical();
        $form = $this->createForm(PeriodicalType::class, $periodical);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($periodical);
            $em->flush();

            $this->addFlash('success', 'The new periodical was created.');
            return $this->redirectToRoute('periodical_show', array('id' => $periodical->getId()));
        }

        return array(
            'periodical' => $periodical,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Periodical entity.
     *
     * @Route("/{id}", name="periodical_show")
     * @Method("GET")
     * @Template()
     * @param Periodical $periodical
     */
    public function showAction(Periodical $periodical) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Periodical::class);

        return array(
            'periodical' => $periodical,
            'next' => $repo->next($periodical),
            'previous' => $repo->previous($periodical),
        );
    }

    /**
     * Displays a form to edit an existing Periodical entity.
     *
     * @Route("/{id}/edit", name="periodical_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Periodical $periodical
     */
    public function editAction(Request $request, Periodical $periodical) {
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(PeriodicalType::class, $periodical);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The periodical has been updated.');
            return $this->redirectToRoute('periodical_show', array('id' => $periodical->getId()));
        }

        return array(
            'periodical' => $periodical,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Periodical entity.
     *
     * @Route("/{id}/delete", name="periodical_delete")
     * @Method("GET")
     * @param Request $request
     * @param Periodical $periodical
     */
    public function deleteAction(Request $request, Periodical $periodical) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($periodical);
        $em->flush();
        $this->addFlash('success', 'The periodical was deleted.');

        return $this->redirectToRoute('periodical_index');
    }

}
