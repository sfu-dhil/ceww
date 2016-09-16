<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\PublicationType;
use AppBundle\Form\PublicationTypeType;

/**
 * PublicationType controller.
 *
 * @Route("/admin/publication_type")
 */
class PublicationTypeController extends Controller
{
    /**
     * Lists all PublicationType entities.
     *
     * @Route("/", name="admin_publication_type_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:PublicationType e ORDER BY e.label';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $publicationTypes = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'publicationTypes' => $publicationTypes,
        );
    }
    /**
     * Search for PublicationType entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:PublicationType repository. Replace the fieldName with
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
     * @Route("/search", name="admin_publication_type_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:PublicationType');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$publicationTypes = $paginator->paginate($query, $request->query->getint('page', 1), 25);
		} else {
			$publicationTypes = array();
		}

        return array(
            'publicationTypes' => $publicationTypes,
			'q' => $q,
        );
    }

    /**
     * Creates a new PublicationType entity.
     *
     * @Route("/new", name="admin_publication_type_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $publicationType = new PublicationType();
        $form = $this->createForm('AppBundle\Form\PublicationTypeType', $publicationType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publicationType);
            $em->flush();

            $this->addFlash('success', 'The new publicationType was created.');
            return $this->redirectToRoute('admin_publication_type_show', array('id' => $publicationType->getId()));
        }

        return array(
            'publicationType' => $publicationType,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a PublicationType entity.
     *
     * @Route("/{id}", name="admin_publication_type_show")
     * @Method("GET")
     * @Template()
	 * @param PublicationType $publicationType
     */
    public function showAction(PublicationType $publicationType)
    {

        return array(
            'publicationType' => $publicationType,
        );
    }

    /**
     * Displays a form to edit an existing PublicationType entity.
     *
     * @Route("/{id}/edit", name="admin_publication_type_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param PublicationType $publicationType
     */
    public function editAction(Request $request, PublicationType $publicationType)
    {
        $editForm = $this->createForm('AppBundle\Form\PublicationTypeType', $publicationType);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publicationType);
            $em->flush();
            $this->addFlash('success', 'The publicationType has been updated.');
            return $this->redirectToRoute('admin_publication_type_show', array('id' => $publicationType->getId()));
        }

        return array(
            'publicationType' => $publicationType,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a PublicationType entity.
     *
     * @Route("/{id}/delete", name="admin_publication_type_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param PublicationType $publicationType
     */
    public function deleteAction(Request $request, PublicationType $publicationType)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($publicationType);
        $em->flush();
        $this->addFlash('success', 'The publicationType was deleted.');

        return $this->redirectToRoute('admin_publication_type_index');
    }
}
