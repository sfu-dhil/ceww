<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Periodical;
use AppBundle\Form\PeriodicalType;

/**
 * Periodical controller.
 *
 * @Route("/periodical")
 */
class PeriodicalController extends Controller
{
    /**
     * Lists all Periodical entities.
     *
     * @Route("/", name="periodical_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Periodical::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $periodicals = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'periodicals' => $periodicals,
        );
    }
    /**
     * Search for Periodical entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Periodical repository. Replace the fieldName with
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
     * @Route("/search", name="periodical_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Periodical');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$periodicals = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$periodicals = array();
		}

        return array(
            'periodicals' => $periodicals,
			'q' => $q,
        );
    }
    /**
     * Full text search for Periodical entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Periodical repository. Replace the fieldName with
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
	 * fulltext indexes on your Periodical entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="periodical_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Periodical');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$periodicals = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$periodicals = array();
		}

        return array(
            'periodicals' => $periodicals,
			'q' => $q,
        );
    }

    /**
     * Creates a new Periodical entity.
     *
     * @Route("/new", name="periodical_new")
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
        $periodical = new Periodical();
        $form = $this->createForm(PeriodicalType::class, $periodical);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($periodical);
            $em->flush();

            $this->addFlash('success', 'The new periodical was created.');
            return $this->redirectToRoute('periodical_show', array('id' => $periodical->getId()));
        }

        return array(
            'periodical' => $periodical,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Periodical entity.
     *
     * @Route("/{id}", name="periodical_show")
     * @Method("GET")
     * @Template()
	 * @param Periodical $periodical
     */
    public function showAction(Periodical $periodical)
    {

        return array(
            'periodical' => $periodical,
        );
    }

    /**
     * Displays a form to edit an existing Periodical entity.
     *
     * @Route("/{id}/edit", name="periodical_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Periodical $periodical
     */
    public function editAction(Request $request, Periodical $periodical)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(PeriodicalType::class, $periodical);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The periodical has been updated.');
            return $this->redirectToRoute('periodical_show', array('id' => $periodical->getId()));
        }

        return array(
            'periodical' => $periodical,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Periodical entity.
     *
     * @Route("/{id}/delete", name="periodical_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Periodical $periodical
     */
    public function deleteAction(Request $request, Periodical $periodical)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($periodical);
        $em->flush();
        $this->addFlash('success', 'The periodical was deleted.');

        return $this->redirectToRoute('periodical_index');
    }
}
