<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Author;
use AppBundle\Form\AuthorType;

/**
 * Author controller.
 *
 * @Route("/admin/author")
 */
class AuthorController extends Controller
{
    /**
     * Lists all Author entities.
     *
     * @Route("/", name="admin_author_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Author e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $authors = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'authors' => $authors,
        );
    }

    /**
     * Creates a new Author entity.
     *
     * @Route("/new", name="admin_author_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $author = new Author();
        $form = $this->createForm('AppBundle\Form\AuthorType', $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            $this->addFlash('success', 'The new author was created.');
            return $this->redirectToRoute('admin_author_show', array('id' => $author->getId()));
        }

        return array(
            'author' => $author,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Author entity.
     *
     * @Route("/{id}", name="admin_author_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Author $author)
    {

        return array(
            'author' => $author,
        );
    }

    /**
     * Displays a form to edit an existing Author entity.
     *
     * @Route("/{id}/edit", name="admin_author_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction(Request $request, Author $author)
    {
        $editForm = $this->createForm('AppBundle\Form\AuthorType', $author);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();
            $this->addFlash('success', 'The author has been updated.');
            return $this->redirectToRoute('admin_author_show', array('id' => $author->getId()));
        }

        return array(
            'author' => $author,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Author entity.
     *
     * @Route("/{id}/delete", name="admin_author_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, Author $author)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();
        $this->addFlash('success', 'The author was deleted.');

        return $this->redirectToRoute('admin_author_index');
    }
}
