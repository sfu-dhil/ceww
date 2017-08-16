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
 * @Route("/place")
 */
class PlaceController extends Controller
{
    /**
     * Lists all Place entities.
     *
     * @Route("/", name="place_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Place::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
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
     * @Route("/search", name="place_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Place');
		$q = $request->query->get('q');
		if($q) {
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
     * Full text search for Place entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Place repository. Replace the fieldName with
	 * something appropriate, and adjust the generated fulltext.html.twig
	 * template.
	 * 
	//    public function fulltextQuery($q) {
	//        $qb = $this->createQueryBuilder('e');
	//        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
	//        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
	//        $qb->orderBy('score', 'desc');
	//        $qb->setParameter('q', $q);
	//        return $qb->getQuery();
	//    }	 
	 * 
	 * Requires a MatchAgainst function be added to doctrine, and appropriate
	 * fulltext indexes on your Place entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="place_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Place');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
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
     * Creates a new Place entity.
     *
     * @Route("/new", name="place_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            $this->addFlash('success', 'The new place was created.');
            return $this->redirectToRoute('place_show', array('id' => $place->getId()));
        }

        return array(
            'place' => $place,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/{id}", name="place_show")
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
     * @Route("/{id}/edit", name="place_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Place $place
     */
    public function editAction(Request $request, Place $place)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(PlaceType::class, $place);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The place has been updated.');
            return $this->redirectToRoute('place_show', array('id' => $place->getId()));
        }

        return array(
            'place' => $place,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Place entity.
     *
     * @Route("/{id}/delete", name="place_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Place $place
     */
    public function deleteAction(Request $request, Place $place)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($place);
        $em->flush();
        $this->addFlash('success', 'The place was deleted.');

        return $this->redirectToRoute('place_index');
    }
}
