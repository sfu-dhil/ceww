<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Person;
use AppBundle\Entity\Publisher;
use AppBundle\Form\PublisherType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Publisher controller.
 *
 * @Route("/publisher")
 */
class PublisherController extends Controller {

    /**
     * Lists all Publisher entities.
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     *
     * @return array
     *   Array data for the template processor.
     *
     * @Route("/", name="publisher_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Publisher::class, 'e')->orderBy('e.name', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $publishers = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'publishers' => $publishers,
        );
    }

    /**
     * Typeahead API endpoint for Publisher entities.
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     *
     * @Route("/typeahead", name="publisher_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Publisher::class);
        $data = [];
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Search for Publisher entities.
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     *
     * @Route("/search", name="publisher_search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Publisher');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $publishers = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $publishers = array();
        }

        return array(
            'publishers' => $publishers,
            'q' => $q,
        );
    }

    /**
     * Creates a new Publisher entity.
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     *
     * @return array|RedirectResponse
     *   Array data for the template processor or a redirect to the Publisher.
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="publisher_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request) {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publisher);
            $em->flush();

            $this->addFlash('success', 'The new publisher was created.');
            return $this->redirectToRoute('publisher_show', array('id' => $publisher->getId()));
        }

        return array(
            'publisher' => $publisher,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Publisher entity in a popup.
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     *
     * @return array|RedirectResponse
     *   Array data for the template processor or a redirect to the Artwork.
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="publisher_new_popup")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Publisher entity.
     *
     * @param Publisher $publisher
     *   The Publisher to show.
     *
     * @return array
     *   Array data for the template processor.
     *
     * @Route("/{id}", name="publisher_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Publisher $publisher, EntityManagerInterface $em) {

        return array(
            'publisher' => $publisher,
            'people' => $em->getRepository(Person::class)->byPublisher($publisher),
        );
    }

    /**
     * Displays a form to edit an existing Publisher entity.
     *
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     * @param Publisher $publisher
     *   The Publisher to edit.
     *
     * @return array|RedirectResponse
     *   Array data for the template processor or a redirect to the Publisher.
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="publisher_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Publisher $publisher) {
        $editForm = $this->createForm(PublisherType::class, $publisher);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The publisher has been updated.');
            return $this->redirectToRoute('publisher_show', array('id' => $publisher->getId()));
        }

        return array(
            'publisher' => $publisher,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Publisher entity.
     *
     *
     * @param Request $request
     *   Dependency injected HTTP request object.
     * @param Publisher $publisher
     *   The Publisher to delete.
     *
     * @return array|RedirectResponse
     *   A redirect to the publisher_index.
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="publisher_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Publisher $publisher) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($publisher);
        $em->flush();
        $this->addFlash('success', 'The publisher was deleted.');

        return $this->redirectToRoute('publisher_index');
    }

}
