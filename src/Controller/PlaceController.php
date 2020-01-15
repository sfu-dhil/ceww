<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use App\Services\Merger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Place controller.
 *
 * @Route("/place")
 */
class PlaceController extends AbstractController {
    /**
     * Lists all Place entities.
     *
     * @Route("/", name="place_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     */
    public function indexAction(Request $request) {
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
     * @param PlaceRepository $repo
     * @Route("/typeahead", name="place_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, PlaceRepository $repo) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse(array());
        }
        $data = array();
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = array(
                'id' => $result->getId(),
                'text' => $result->getName(),
            );
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/search", name="place_search", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     * @param PlaceRepository $repo
     */
    public function searchAction(Request $request, PlaceRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
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
     * @Route("/new", name="place_new", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     *
     * @param Request $request
     */
    public function newAction(Request $request) {
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
     * @Route("/new_popup", name="place_new_popup", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     *
     * @param Request $request
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/{id}", name="place_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Place $place
     */
    public function showAction(Place $place) {
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
     * @Route("/{id}/edit", name="place_edit", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     *
     * @param Request $request
     * @param Place $place
     */
    public function editAction(Request $request, Place $place) {
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
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Template()
     *
     * @param Place $place
     * @param Request $request
     * @param Merger $merger
     * @param PlaceRepository $repo
     */
    public function mergeAction(Request $request, Place $place, Merger $merger, PlaceRepository $repo) {
        if ('POST' === $request->getMethod()) {
            $places = $repo->findBy(array('id' => $request->request->get('places')));
            $count = count($places);
            $merger->places($place, $places);
            $this->addFlash('success', "Merged {$count} places into {$place->getName()}.");

            return $this->redirect($this->generateUrl('place_show', array('id' => $place->getId())));
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
     * @Route("/{id}/delete", name="place_delete", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     *
     * @param Request $request
     * @param Place $place
     */
    public function deleteAction(Request $request, Place $place) {
        if ( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
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
