<?php

namespace PubBundle\Controller;

use AppBundle\Entity\Alias;
use FeedbackBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/fulltext", name="alias_fulltext")
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
     * Finds and displays a Alias entity.
     *
     * @Route("/{id}", name="alias_show")
     * @Method({"GET","POST"})
     * @Template()
     * @param Alias $alias
     */
    public function showAction(Request $request, Alias $alias) {

        $comment = new Comment();
        $form = $this->createForm('FeedbackBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('feedback.comment')->addComment($alias, $comment);
            $this->addFlash('success', 'Thank you for your suggestion.');
            return $this->redirect($this->generateUrl('alias_show', array('id' => $alias->getId())));
        }
        
        return array(
            'form' => $form->createView(),
            'alias' => $alias,
        );
    }

}
