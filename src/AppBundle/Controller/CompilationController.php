<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Compilation;
use AppBundle\Form\CompilationType;

/**
 * Compilation controller.
 *
 * @Route("/compilation")
 */
class CompilationController extends Controller
{
    /**
     * Lists all Compilation entities.
     *
     * @Route("/", name="compilation_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Compilation::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $compilations = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'compilations' => $compilations,
        );
    }
    /**
     * Search for Compilation entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Compilation repository. Replace the fieldName with
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
     * @Route("/search", name="compilation_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Compilation');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$compilations = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$compilations = array();
		}

        return array(
            'compilations' => $compilations,
			'q' => $q,
        );
    }
    /**
     * Full text search for Compilation entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Compilation repository. Replace the fieldName with
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
	 * fulltext indexes on your Compilation entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="compilation_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Compilation');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$compilations = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$compilations = array();
		}

        return array(
            'compilations' => $compilations,
			'q' => $q,
        );
    }

    /**
     * Creates a new Compilation entity.
     *
     * @Route("/new", name="compilation_new")
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
        $compilation = new Compilation();
        $form = $this->createForm(CompilationType::class, $compilation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($compilation);
            $em->flush();

            $this->addFlash('success', 'The new compilation was created.');
            return $this->redirectToRoute('compilation_show', array('id' => $compilation->getId()));
        }

        return array(
            'compilation' => $compilation,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Compilation entity.
     *
     * @Route("/{id}", name="compilation_show")
     * @Method("GET")
     * @Template()
	 * @param Compilation $compilation
     */
    public function showAction(Compilation $compilation)
    {

        return array(
            'compilation' => $compilation,
        );
    }

    /**
     * Displays a form to edit an existing Compilation entity.
     *
     * @Route("/{id}/edit", name="compilation_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Compilation $compilation
     */
    public function editAction(Request $request, Compilation $compilation)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(CompilationType::class, $compilation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The compilation has been updated.');
            return $this->redirectToRoute('compilation_show', array('id' => $compilation->getId()));
        }

        return array(
            'compilation' => $compilation,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Compilation entity.
     *
     * @Route("/{id}/delete", name="compilation_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Compilation $compilation
     */
    public function deleteAction(Request $request, Compilation $compilation)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($compilation);
        $em->flush();
        $this->addFlash('success', 'The compilation was deleted.');

        return $this->redirectToRoute('compilation_index');
    }
}
