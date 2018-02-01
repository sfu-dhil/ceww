<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Place;
use AppBundle\Form\PlaceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $qb->select('e')->from(Place::class, 'e')->orderBy('e.sortableName', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $places = $paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return array(
            'places' => $places,
        );
    }
    
    /**
     * @param Request $request
     * @Route("/typeahead", name="place_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Place');
        $data = [];
        foreach($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => $result->getName(),
            ];
        }
        
        return new JsonResponse($data);
    }
    
    /**
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
			$places = $paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));
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
        if( ! $this->isGranted('ROLE_CONTENT_EDITOR')) {
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
     * Creates a new Place entity.
     *
     * @Route("/new_popup", name="place_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
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
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Place::class);
        
        return array(
            'place' => $place,
            'next' => $repo->next($place),
            'previous' => $repo->previous($place),
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
        if( ! $this->isGranted('ROLE_CONTENT_EDITOR')) {
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
     * Finds and displays a Place entity.
     *
     * @Route("/{id}/merge", name="place_merge")
     * @Method({"GET","POST"})
     * @Template()
     * @param Place $place
     */
    public function mergeAction(Request $request, Place $place) {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Place::class);
        
        if($request->getMethod() === 'POST') {
            $places = $repo->findBy(array('id' => $request->request->get('places')));
            $count = count($places);
            $merger = $this->container->get('ceww.merger');
            $merger->places($place, $places);
            $this->addFlash('success', "Merged {$count} places into {$place->getName()}.");
            return $this->redirect($this->generateUrl('place_show', ['id' => $place->getId()]));
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
