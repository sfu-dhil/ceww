<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Publication;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return [];
    }
    
    /**
     * Search for Periodical entities.
     *
     * @Route("/search", name="search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Publication::class);
        $q = $request->query->get('q');
        if ($q) {
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

    
}
