<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Contribution;
use AppBundle\Form\BookType;
use AppBundle\Form\ContributionType;
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
        $books = $paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return array(
            'books' => $books,
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
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
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
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Book::class);

        return array(
            'book' => $book,
            'next' => $repo->next($book),
            'previous' => $repo->previous($book),
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
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
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

    /**
     * Creates a new Book contribution entity.
     *
     * @Route("/{id}/contributions/new", name="book_new_contribution")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Book $book
     */
    public function newContribution(Request $request, Book $book) {
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $contribution = new Contribution();

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contribution->setPublication($book);
            $em = $this->getDoctrine()->getManager();
            $em->persist($contribution);
            $em->flush();

            $this->addFlash('success', 'The new contribution was created.');
            return $this->redirectToRoute('book_show_contributions', array('id' => $book->getId()));
        }

        return array(
            'book' => $book,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Show book contributions list with edit/delete action items
     * 
     * @Route("/{id}/contributions", name="book_show_contributions")
     * @Method("GET")
     * @Template()
     * @param Book $book
     */
    public function showContributions(Book $book) {
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        return array(
            'book' => $book,
        );
    }
    
    /**
     * Displays a form to edit an existing book Contribution entity.
     *
     * @Route("/contributions/{id}/edit", name="book_edit_contributions")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Contribution $contribution
     */
    public function editContribution(Request $request, Contribution $contribution) {
        if (!$this->isGranted('ROLE_CONTENT_EDITOR')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $editForm = $this->createForm(ContributionType::class, $contribution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The contribution has been updated.');
            return $this->redirectToRoute('book_show_contributions', array('id' => $contribution->getPublicationId()));
        }

        return array(
            'contribution' => $contribution,
            'edit_form' => $editForm->CreateView(),
        );
    }

    /**
     * Deletes a book Contribution entity.
     *
     * @Route("/contributions/{id}/delete", name="book_delete_contributions")
     * @Method("GET")
     * @param Request $request
     * @param Contribution $contribution
     */
    public function deleteContribution(Request $request, Contribution $contribution) {
        if (!$this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($contribution);
        $em->flush();
        $this->addFlash('success', 'The contribution was deleted.');

        return $this->redirectToRoute('book_show_contributions', array('id' => $contribution->getPublicationId()));
    }

}
