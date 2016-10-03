<?php

namespace PubBundle\Controller;

use AppBundle\Entity\Author;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Author controller.
 *
 * @Route("/author")
 */
class AuthorController extends Controller
{

    /**
     * Lists all Author entities.
     *
     * @Route("/", name="author_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Author e ORDER BY e.sortableName';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $authors = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'authors' => $authors,
        );
    }

    /**
     * Search for Author entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Author repository. Replace the fieldName with
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
     * @Route("/search", name="author_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Author');
        $q = $request->query->get('q');
        if($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $authors = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $authors = array();
        }

        return array(
            'authors' => $authors,
            'q' => $q,
        );
    }

    /**
     * Finds and displays a Author entity.
     *
     * @Route("/{id}", name="author_show")
     * @Method("GET")
     * @Template()
     * @param Author $author
     */
    public function showAction(Author $author) {

        return array(
            'author' => $author,
        );
    }

}
