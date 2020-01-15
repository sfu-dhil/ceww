<?php

namespace App\Controller;

use App\Entity\Alias;
use App\Form\AliasType;
use App\Repository\AliasRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Alias controller.
 *
 * @Route("/alias")
 */
class AliasController extends Controller {
    /**
     * Lists all Alias entities.
     *
     * @Route("/", name="alias_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Alias::class, 'e')->orderBy('e.sortableName', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $aliases = $paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return array(
            'aliases' => $aliases,
        );
    }

    /**
     * Search for Alias entities.
     *
     * @Route("/search", name="alias_search", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     * @param AliasRepository $repo
     *
     * @return array
     */
    public function searchAction(Request $request, AliasRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $aliases = $paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));
        } else {
            $aliases = array();
        }

        return array(
            'aliases' => $aliases,
            'q' => $q,
        );
    }

    /**
     * @param Request $request
     * @param AliasRepository $repo
     * @Route("/typeahead", name="alias_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, AliasRepository $repo) {
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
     * Creates a new Alias entity.
     *
     * @Route("/new", name="alias_new", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     *
     * @param Request $request
     */
    public function newAction(Request $request) {
        $alias = new Alias();
        $form = $this->createForm(AliasType::class, $alias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alias);
            $em->flush();

            $this->addFlash('success', 'The new alias was created.');

            return $this->redirectToRoute('alias_show', array('id' => $alias->getId()));
        }

        return array(
            'alias' => $alias,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Alias entity.
     *
     * @Route("/new", name="alias_new_popup", methods={"GET","POST"})
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
     * Finds and displays a Alias entity.
     *
     * @Route("/{id}", name="alias_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Alias $alias
     */
    public function showAction(Alias $alias) {
        return array(
            'alias' => $alias,
        );
    }

    /**
     * Displays a form to edit an existing Alias entity.
     *
     * @Route("/{id}/edit", name="alias_edit", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     *
     * @param Request $request
     * @param Alias $alias
     */
    public function editAction(Request $request, Alias $alias) {
        $editForm = $this->createForm(AliasType::class, $alias);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The alias has been updated.');

            return $this->redirectToRoute('alias_show', array('id' => $alias->getId()));
        }

        return array(
            'alias' => $alias,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Alias entity.
     *
     * @Route("/{id}/delete", name="alias_delete", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     *
     * @param Request $request
     * @param Alias $alias
     */
    public function deleteAction(Request $request, Alias $alias) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($alias);
        $em->flush();
        $this->addFlash('success', 'The alias was deleted.');

        return $this->redirectToRoute('alias_index');
    }
}
