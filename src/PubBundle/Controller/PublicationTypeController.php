<?php

namespace PubBundle\Controller;

use AppBundle\Entity\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * PublicationType controller.
 *
 * @Route("/publication_type")
 */
class PublicationTypeController extends Controller
{
    /**
     * Lists all PublicationType entities.
     *
     * @Route("/", name="admin_publication_type_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:PublicationType e ORDER BY e.label';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $publicationTypes = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'publicationTypes' => $publicationTypes,
        );
    }
    /**
     * Search for PublicationType entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:PublicationType repository. Replace the fieldName with
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
     * @Route("/search", name="admin_publication_type_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:PublicationType');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$publicationTypes = $paginator->paginate($query, $request->query->getint('page', 1), 25);
		} else {
			$publicationTypes = array();
		}

        return array(
            'publicationTypes' => $publicationTypes,
			'q' => $q,
        );
    }

    /**
     * Finds and displays a PublicationType entity.
     *
     * @Route("/{id}", name="admin_publication_type_show")
     * @Method("GET")
     * @Template()
	 * @param PublicationType $publicationType
     */
    public function showAction(PublicationType $publicationType)
    {

        return array(
            'publicationType' => $publicationType,
        );
    }

}
