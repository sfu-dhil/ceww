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
     * Full text search for Author entities.
     *
     * Requires a MatchAgainst function be added to doctrine, and appropriate
     * fulltext indexes on your Author entity.
     *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
     *
     *
     * @Route("/fulltext", name="author_fulltext")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function fulltextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Author');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->fulltextQuery($q);
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
