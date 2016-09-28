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
     * @param Request $request
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
     * Search for Place entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Place repository. Replace the fieldName with
     * something appropriate, and adjust the generated search.html.twig
     * template.
     *
      //    public function searchQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->where("e.fieldName like '%$q%'");
      //        return $qb->getQuery();
      //    }
     *
     *
     * @Route("/search", name="admin_place_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Place');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $places = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $places = array();
        }

        return array(
            'places' => $places,
            'q' => $q,
        );
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/merge/{id}", name="admin_place_merge")
     * @Method({"GET","POST"})
     * @Template()
     * @param Place $place
     */
    public function mergeAction(Request $request, Place $place)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Place');
        
        if ($request->getMethod() === 'POST') {
            $places = $repo->findBy(array('id' => $request->request->get('places')));
            $count = count($places);
            $merger = $this->container->get('ceww.merger');
            $merger->places($place, $places);
            $this->addFlash('success', "Merged {$count} places into {$place->getName()}.");
            return $this->redirect($this->generateUrl('admin_place_show', ['id' => $place->getId()]));
        }
        
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $places = $query->execute();
        } else {
            $places = array();
        }

        return array(
            'place' => $place,
            'places' => $places,
            'q' => $q,
        );
    }
    /**
     * Creates a new Place entity.
     *
     * @Route("/new", name="admin_place_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
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
     * @param Place $place
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
     * @param Request $request
     * @param Place $place
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
     * @param Request $request
     * @param Place $place
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
