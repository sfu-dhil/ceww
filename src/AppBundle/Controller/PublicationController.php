<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Publication;
use AppBundle\Form\PublicationType;

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
        $dql = 'SELECT e FROM AppBundle:Publication e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $publications = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'publications' => $publications,
        );
    }
    /**
     * Search for Publication entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Publication repository. Replace the fieldName with
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
     * Full text search for Publication entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Publication repository. Replace the fieldName with
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
	 * fulltext indexes on your Publication entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="publication_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Publication');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
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
