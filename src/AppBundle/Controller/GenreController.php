<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genre;
use AppBundle\Form\GenreType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Genre controller.
 *
 * @Route("/genre")
 */
class GenreController extends Controller
{
    /**
     * Lists all Genre entities.
     *
     * @Route("/", name="genre_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Genre::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $genres = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'genres' => $genres,
        );
    }
    
    /**
     * @param Request $request
     * @Route("/typeahead", name="genre_typeahead")
     * @Method("GET")
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Genre');
        $data = [];
        foreach($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => $result->getName(),
            ];
        }
        
        return new JsonResponse($data);
    }
    
    /**
     * Creates a new Genre entity.
     *
     * @Route("/new", name="genre_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($genre);
            $em->flush();

            $this->addFlash('success', 'The new genre was created.');
            return $this->redirectToRoute('genre_show', array('id' => $genre->getId()));
        }

        return array(
            'genre' => $genre,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Genre entity.
     *
     * @Route("/{id}", name="genre_show")
     * @Method("GET")
     * @Template()
	 * @param Genre $genre
     */
    public function showAction(Genre $genre)
    {

        return array(
            'genre' => $genre,
        );
    }

    /**
     * Displays a form to edit an existing Genre entity.
     *
     * @Route("/{id}/edit", name="genre_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Genre $genre
     */
    public function editAction(Request $request, Genre $genre)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(GenreType::class, $genre);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The genre has been updated.');
            return $this->redirectToRoute('genre_show', array('id' => $genre->getId()));
        }

        return array(
            'genre' => $genre,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Genre entity.
     *
     * @Route("/{id}/delete", name="genre_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Genre $genre
     */
    public function deleteAction(Request $request, Genre $genre)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($genre);
        $em->flush();
        $this->addFlash('success', 'The genre was deleted.');

        return $this->redirectToRoute('genre_index');
    }
}
