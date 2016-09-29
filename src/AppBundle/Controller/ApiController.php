<?php

namespace AppBundle\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
