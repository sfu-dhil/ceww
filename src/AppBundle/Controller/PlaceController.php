<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Place;
use AppBundle\Form\PlaceType;

/**
 * Place controller.
 *
 * @Route("/admin/place")
 */
class PlaceController extends Controller
{
    /**
     * Lists all Place entities.
     *
     * @Route("/", name="admin_place_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Place e ORDER BY e.name';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $places = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'places' => $places,
        );
    }

    /**
     * Creates a new Place entity.
     *
     * @Route("/new", name="admin_place_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $place = new Place();
        $form = $this->createForm('AppBundle\Form\PlaceType', $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            $this->addFlash('success', 'The new place was created.');
            return $this->redirectToRoute('admin_place_show', array('id' => $place->getId()));
        }

        return array(
            'place' => $place,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/{id}", name="admin_place_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Place $place)
    {

        return array(
            'place' => $place,
        );
    }
    
    /**
     * Displays a form to edit an existing Place entity.
     *
     * @Route("/{id}/edit", name="admin_place_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Place $place)
    {
        $editForm = $this->createForm('AppBundle\Form\PlaceType', $place);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();
            $this->addFlash('success', 'The place has been updated.');
            return $this->redirectToRoute('admin_place_show', array('id' => $place->getId()));
        }

        return array(
            'place' => $place,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Place entity.
     *
     * @Route("/{id}/delete", name="admin_place_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Place $place)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($place);
        $em->flush();
        $this->addFlash('success', 'The place was deleted.');

        return $this->redirectToRoute('admin_place_index');
    }
}
