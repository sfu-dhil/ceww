<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DateYear;
use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Person controller.
 *
 * @Route("/person")
 */
class PersonController extends Controller {

    /**
     * Lists all Person entities.
     *
     * @Route("/", name="person_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Person::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $people = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'people' => $people,
        );
    }

    /**
     * Search for Person entities.
     *
     * @Route("/search", name="person_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $people = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $people = array();
        }

        return array(
            'people' => $people,
            'q' => $q,
        );
    }

    /**
     * Creates a new Person entity.
     *
     * @Route("/new", name="person_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            $this->addFlash('success', 'The new person was created.');
            return $this->redirectToRoute('person_show', array('id' => $person->getId()));
        }

        return array(
            'person' => $person,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Person entity.
     *
     * @Route("/{id}", name="person_show")
     * @Method("GET")
     * @Template()
     * @param Person $person
     */
    public function showAction(Person $person) {

        return array(
            'person' => $person,
        );
    }

    /**
     * Displays a form to edit an existing Person entity.
     *
     * @Route("/{id}/edit", name="person_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Person $person
     */
    public function editAction(Request $request, Person $person) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(PersonType::class, $person, array(
            'router' => $this->container->get('router')
        ));

        // DateYear objects.
        if (($birthYear = $person->getBirthDate())) {
            $editForm['birthYear']->setData($birthYear->getValue());
        }
        if (($deathYear = $person->getDeathDate())) {
            $editForm['deathYear']->setData($deathYear->getValue());
        }
        
        // typeahead fields
        if (($birthPlace = $person->getBirthPlace())) {
            $editForm['birthPlace']->setData($birthPlace->getName());
            $editForm['birthPlace_id']->setData($birthPlace->getId());
        }
        if (($deathPlace = $person->getDeathPlace())) {
            $editForm['deathPlace']->setData($deathPlace->getName());
            $editForm['deathPlace_id']->setData($deathPlace->getId());
        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // DateYear objects
            if(!$editForm['birthYear']->getData()) {
                $person->setBirthDate(null);
            } elseif(!$person->getBirthDate() || ($editForm['birthYear']->getData() !== $person->getBirthDate()->getValue())) {
                $dateYear = new DateYear();
                $em->persist($dateYear);
                $dateYear->setValue($editForm['birthYear']->getData());
                $person->setBirthDate($dateYear);
            }
            
            if(!$editForm['deathYear']->getData()) {
                $person->setDeathDate(null);
            } elseif(!$person->getDeathDate() || ($editForm['deathYear']->getData() !== $person->getDeathDate()->getValue())) {
                $dateYear = new DateYear();
                $em->persist($dateYear);
                $dateYear->setValue($editForm['deathYear']->getData());
                $person->setDeathDate($dateYear);
            }
            
            // typeahead fields
            if(($birthPlaceId = $editForm['birthPlace_id']->getData())) {
                $person->setBirthPlace($em->find('AppBundle:Place', $birthPlaceId));
            }
            if(($deathPlaceId = $editForm['deathPlace_id']->getData())) {
                $person->setDeathPlace($em->find('AppBundle:Place', $deathPlaceId));
            }

            $em->flush();
            $this->addFlash('success', 'The person has been updated.');
            return $this->redirectToRoute('person_show', array('id' => $person->getId()));
        }

        return array(
            'person' => $person,
            'edit_form' => $editForm->createView(),
        );
    }
    
    /**
     * Deletes a Person entity.
     *
     * @Route("/{id}/delete", name="person_delete")
     * @Method("GET")
     * @param Request $request
     * @param Person $person
     */
    public function deleteAction(Request $request, Person $person) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();
        $this->addFlash('success', 'The person was deleted.');

        return $this->redirectToRoute('person_index');
    }

}
