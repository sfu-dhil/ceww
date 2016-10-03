<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Alias;
use AppBundle\Form\AliasType;

/**
 * Alias controller.
 *
 * @Route("/admin/alias")
 */
class AliasController extends Controller
{

    /**
     * Lists all Alias entities.
     *
     * @Route("/", name="admin_alias_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array for template processing
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Alias e ORDER BY e.name';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $aliases = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'aliases' => $aliases,
        );
    }

    /**
     * Search for Alias entities.
     *
     * @Route("/search", name="admin_alias_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Alias');
        $q = $request->query->get('q');
        if($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $aliases = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $aliases = array();
        }

        return array(
            'aliases' => $aliases,
            'q' => $q,
        );
    }

    /**
     * Full text search for Alias entities.
     *
     * @Route("/fulltext", name="admin_alias_fulltext")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function fulltextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Alias');
        $q = $request->query->get('q');
        if($q) {
            $query = $repo->fulltextQuery($q);
            $paginator = $this->get('knp_paginator');
            $aliases = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $aliases = array();
        }

        return array(
            'aliases' => $aliases,
            'q' => $q,
        );
    }

    /**
     * Creates a new Alias entity.
     *
     * @Route("/new", name="admin_alias_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $alias = new Alias();
        $form = $this->createForm('AppBundle\Form\AliasType', $alias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alias);
            $em->flush();

            $this->addFlash('success', 'The new alias was created.');
            return $this->redirectToRoute('admin_alias_show', array('id' => $alias->getId()));
        }

        return array(
            'alias' => $alias,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Alias entity.
     *
     * @Route("/{id}", name="admin_alias_show")
     * @Method("GET")
     * @Template()
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
     * @Route("/{id}/edit", name="admin_alias_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Alias   $alias
     */
    public function editAction(Request $request, Alias $alias) {
        $editForm = $this->createForm('AppBundle\Form\AliasType', $alias);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alias);
            $em->flush();
            $this->addFlash('success', 'The alias has been updated.');
            return $this->redirectToRoute('admin_alias_show', array('id' => $alias->getId()));
        }

        return array(
            'alias' => $alias,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Alias entity.
     *
     * @Route("/{id}/delete", name="admin_alias_delete")
     * @Method("GET")
     * @param Request $request
     * @param Alias   $alias
     */
    public function deleteAction(Request $request, Alias $alias) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($alias);
        $em->flush();
        $this->addFlash('success', 'The alias was deleted.');

        return $this->redirectToRoute('admin_alias_index');
    }
    
}
