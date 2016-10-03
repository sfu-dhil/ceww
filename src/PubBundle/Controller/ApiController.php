<?php

namespace PubBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{

    /**
     * @Route("/typeahead/place", name="typeahead_place")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function placeTypeAheadAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('AppBundle:Place');
        $q = $request->query->get('q');
        $query = $repo->searchQuery($q);
        $results = $query->execute();
        $content = json_encode($results);
        $response = new Response($content, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
