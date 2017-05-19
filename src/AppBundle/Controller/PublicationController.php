<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Publication;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Publication controller.
 *
 * @Route("/publication")
 */
class PublicationController extends Controller
{
    /**
     * Lists all Publication entities.
     *
     * @Route("/", name="publication_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = null;
        if(($categoryName = $request->query->get('category'))) {
            $category = $em->getRepository(Category::class)->findOneBy(array('name' => $categoryName));
        }
        $query = $em->getRepository(Publication::class)->browseQuery($category);
        $paginator = $this->get('knp_paginator');
        $publications = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'publications' => $publications,
            'categories' => $em->getRepository(Category::class)->findBy(array(), array('label' => 'ASC')),
            'filterCategory' => $category,
        );
    }
    /**
     * Search for Publication entities.
     *
     * @Route("/search", name="publication_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Publication');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$publications = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$publications = array();
		}

        return array(
            'publications' => $publications,
			'q' => $q,
        );
    }

    /**
     * Creates a new Publication entity.
     *
     * @Route("/new", name="publication_new")
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
        $publication = new Publication();
        $form = $this->createForm('AppBundle\Form\PublicationType', $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $em->flush();

            $this->addFlash('success', 'The new publication was created.');
            return $this->redirectToRoute('publication_show', array('id' => $publication->getId()));
        }

        return array(
            'publication' => $publication,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Publication entity.
     *
     * @Route("/{id}", name="publication_show")
     * @Method("GET")
     * @Template()
	 * @param Publication $publication
     */
    public function showAction(Publication $publication)
    {

        return array(
            'publication' => $publication,
        );
    }

    /**
     * Displays a form to edit an existing Publication entity.
     *
     * @Route("/{id}/edit", name="publication_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Publication $publication
     */
    public function editAction(Request $request, Publication $publication)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm('AppBundle\Form\PublicationType', $publication);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The publication has been updated.');
            return $this->redirectToRoute('publication_show', array('id' => $publication->getId()));
        }

        return array(
            'publication' => $publication,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Publication entity.
     *
     * @Route("/{id}/delete", name="publication_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Publication $publication
     */
    public function deleteAction(Request $request, Publication $publication)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($publication);
        $em->flush();
        $this->addFlash('success', 'The publication was deleted.');

        return $this->redirectToRoute('publication_index');
    }
}
