<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Status;
use AppBundle\Form\StatusType;

/**
 * Status controller.
 *
 * @Route("status")
 */
class StatusController extends Controller
{

    /**
     * Lists all Status entities.
     *
     * @Route("/", name="status_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Status e ORDER BY e.label';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $statuses = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'statuses' => $statuses,
        );
    }

    /**
     * Search for Status entities.
     *
     * @Route("/search", name="status_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Status');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $statuses = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $statuses = array();
        }

        return array(
            'statuses' => $statuses,
            'q' => $q,
        );
    }

    /**
     * Full text search for Status entities.
     *
     * @Route("/fulltext", name="status_fulltext")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function fulltextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Status');
        $q = $request->query->get('q');
        if($q) {
            $query = $repo->fulltextQuery($q);
            $paginator = $this->get('knp_paginator');
            $statuses = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $statuses = array();
        }

        return array(
            'statuses' => $statuses,
            'q' => $q,
        );
    }

    /**
     * Creates a new Status entity.
     *
     * @Route("/new", name="status_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $status = new Status();
        $form = $this->createForm('AppBundle\Form\StatusType', $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();

            $this->addFlash('success', 'The new status was created.');
            return $this->redirectToRoute('status_show', array('id' => $status->getId()));
        }

        return array(
            'status' => $status,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Status entity.
     *
     * @Route("/{id}", name="status_show")
     * @Method("GET")
     * @Template()
     * @param Status $status
     */
    public function showAction(Status $status) {

        return array(
            'status' => $status,
        );
    }

    /**
     * Displays a form to edit an existing Status entity.
     *
     * @Route("/{id}/edit", name="status_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Status  $status
     */
    public function editAction(Request $request, Status $status) {
        $editForm = $this->createForm('AppBundle\Form\StatusType', $status);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($status);
            $em->flush();
            $this->addFlash('success', 'The status has been updated.');
            return $this->redirectToRoute('status_show', array('id' => $status->getId()));
        }

        return array(
            'status' => $status,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Status entity.
     *
     * @Route("/{id}/delete", name="status_delete")
     * @Method("GET")
     * @param Request $request
     * @param Status  $status
     */
    public function deleteAction(Request $request, Status $status) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($status);
        $em->flush();
        $this->addFlash('success', 'The status was deleted.');

        return $this->redirectToRoute('status_index');
    }

}
