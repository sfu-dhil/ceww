<?php

namespace PubBundle\Controller;

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
class ApiController extends Controller {

    /**
     * @return Serializer
     */
    private function getSerializer() {
        $encoder = new JsonEncoder();
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory);
        $objectNormalizer->setCircularReferenceHandler(function($object){
            return "";
        });
        $dateTimeNormalizer = new DateTimeNormalizer();

        $serializer = new Serializer([$objectNormalizer, $dateTimeNormalizer], [$encoder]);
        return $serializer;
    }

    /**
     * @Route("/{type}/search", name="api_search")
     * @Method("GET")
     * @param Request $request
     */
    public function searchAction(Request $request, $type) {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('AppBundle:' . ucfirst($type));
        $q = $request->query->get('q');
        $query = $repo->searchQuery($q);
        $results = $query->getArrayResult();
        $serializer = $this->getSerializer();

        $data = $serializer->normalize($results, null, array('groups' => array('public')));
        $content = $serializer->serialize($data, 'json');
        $response = new Response($content, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{type}/{id}", name="api_entity")
     * @Method("GET")
     */
    public function entityAction($type, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->find('AppBundle:' . ucfirst($type), $id);
        $serializer = $this->getSerializer();
        $data = $serializer->normalize($entity, null, array('groups' => array('public')));
        $content = $serializer->serialize($data, 'json');
        $response = new Response($content, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
