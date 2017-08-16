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
 * @Route("/alias")
 */
class AliasController extends Controller
{
    /**
     * Lists all Alias entities.
     *
     * @Route("/", name="alias_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Alias::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $aliases = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'aliases' => $aliases,
        );
    }
    /**
     * Search for Alias entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Alias repository. Replace the fieldName with
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
     * @Route("/search", name="alias_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Alias');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$aliases = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
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
	 * To make this work, add a method like this one to the 
	 * AppBundle:Alias repository. Replace the fieldName with
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
	 * fulltext indexes on your Alias entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="alias_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Alias');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$aliases = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
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
     * @Route("/new", name="alias_new")
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
     * Finds and displays a Alias entity.
     *
     * @Route("/{id}", name="alias_show")
     * @Method("GET")
     * @Template()
	 * @param Alias $alias
     */
    public function showAction(Alias $alias)
    {

        return array(
            'alias' => $alias,
        );
    }

    /**
     * Displays a form to edit an existing Alias entity.
     *
     * @Route("/{id}/edit", name="alias_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Alias $alias
     */
    public function editAction(Request $request, Alias $alias)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
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
     * @Route("/{id}/delete", name="alias_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Alias $alias
     */
    public function deleteAction(Request $request, Alias $alias)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($alias);
        $em->flush();
        $this->addFlash('success', 'The alias was deleted.');

        return $this->redirectToRoute('alias_index');
    }
}
