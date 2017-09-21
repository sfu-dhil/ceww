<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Form\ContributionCollectionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book controller.
 *
 * @Route("/book")
 */
class BookController extends Controller {

    /**
     * Lists all Book entities.
     *
     * @Route("/", name="book_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Book::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $books = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'books' => $books,
        );
    }

    /**
     * Search for Book entities.
     *
     * @Route("/search", name="book_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Book');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $books = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $books = array();
        }

        return array(
            'books' => $books,
            'q' => $q,
        );
    }

    /**
     * Full text search for Book entities.
     *
     * To make this work, add a method like this one to the 
     * AppBundle:Book repository. Replace the fieldName with
     * something appropriate, and adjust the generated fulltext.html.twig
     * template.
     * 
      //    public function fulltextQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
      //        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
      //        $qb->orderBy('score', 'desc');
      //        $qb->setParameter('q', $q);
      //        return $qb->getQuery();
      //    }
     * 
     * Requires a MatchAgainst function be added to doctrine, and appropriate
     * fulltext indexes on your Book entity.
     *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
     *
     *
     * @Route("/fulltext", name="book_fulltext")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function fulltextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Book');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->fulltextQuery($q);
            $paginator = $this->get('knp_paginator');
            $books = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $books = array();
        }

        return array(
            'books' => $books,
            'q' => $q,
        );
    }

    /**
     * Creates a new Book entity.
     *
     * @Route("/new", name="book_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($book->getContributions() as $contribution) {
                $contribution->setPublication($book);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'The new book was created.');
            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return array(
            'book' => $book,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Book entity.
     *
     * @Route("/{id}", name="book_show")
     * @Method("GET")
     * @Template()
     * @param Book $book
     */
    public function showAction(Book $book) {

        return array(
            'book' => $book,
        );
    }

    /**
     * Displays a form to edit an existing Book entity.
     *
     * @Route("/{id}/edit", name="book_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Book $book
     */
    public function editAction(Request $request, Book $book) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach($book->getContributions() as $contribution) {
                $contribution->setPublication($book);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The book has been updated.');
            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Book entity.
     *
     * @Route("/{id}/delete", name="book_delete")
     * @Method("GET")
     * @param Request $request
     * @param Book $book
     */
    public function deleteAction(Request $request, Book $book) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($book);
        $em->flush();
        $this->addFlash('success', 'The book was deleted.');

        return $this->redirectToRoute('book_index');
    }

}
