<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DateYear;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * DateYear controller.
 *
 * @Route("/date_year")
 */
class DateYearController extends Controller
{
    /**
     * Lists all DateYear entities.
     *
     * @Route("/", name="date_year_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:DateYear e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $dateYears = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'dateYears' => $dateYears,
        );
    }

    /**
     * Finds and displays a DateYear entity.
     *
     * @Route("/{id}", name="date_year_show")
     * @Method("GET")
     * @Template()
	 * @param DateYear $dateYear
     */
    public function showAction(DateYear $dateYear)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository(DateYear::class);
        $entity = $repo->getEntity($dateYear);
        $reflection = new ReflectionClass($entity);
        $class = $reflection->getShortName();
        $routeName = strtolower($class) . '_show';
        return array(
            'dateYear' => $dateYear,
            'entity' => $entity,
            'class' => $class,
            'routeName' => $routeName,
        );
    }

}
