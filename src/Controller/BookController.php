<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Contribution;
use App\Entity\Publication;
use App\Form\BookType;
use App\Form\ContributionType;
use App\Repository\BookRepository;
use App\Repository\PublicationRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Book controller.
 *
 * @Route("/book")
 */
class BookController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Book entities.
     *
     * @Route("/", name="book_index", methods={"GET"})
     *
     * @Template
     *
     * @param PublicationRepository $repo
     *
     * @return array|RedirectResponse
     */
    public function indexAction(Request $request, BookRepository $repo) {
        $em = $this->getDoctrine()->getManager();
        $pageSize = $this->getParameter('page_size');

        if ($request->query->has('alpha')) {
            $page = $repo->letterPage($request->query->get('alpha'), Publication::BOOK, $pageSize);

            return $this->redirectToRoute('book_index', ['page' => $page]);
        }
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Book::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();
        $books = $this->paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));
        $letterIndex = [];

        foreach ($books as $book) {
            $title = $book->getSortableTitle();
            if ( ! $title) {
                continue;
            }
            $letterIndex[mb_convert_case($title[0], MB_CASE_UPPER)] = 1;
        }

        return [
            'books' => $books,
            'activeLetters' => array_keys($letterIndex),
        ];
    }

    /**
     * Creates a new Book entity.
     *
     * @Route("/new", name="book_new", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function newAction(Request $request) {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($book->getContributions() as $contribution) {
                $contribution->setPublication($book);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'The new book was created.');

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return [
            'book' => $book,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Book entity.
     *
     * @Route("/{id}", name="book_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Book $book) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Book::class);

        return [
            'book' => $book,
            'next' => $repo->next($book),
            'previous' => $repo->previous($book),
        ];
    }

    /**
     * Displays a form to edit an existing Book entity.
     *
     * @Route("/{id}/edit", name="book_edit", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function editAction(Request $request, Book $book) {
        if ( ! $this->isGranted('ROLE_CONTENT_EDITOR')) {
            $this->addFlash('danger', 'You must login to access this page.');

            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $editForm = $this->createForm(BookType::class, $book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($book->getContributions() as $contribution) {
                $contribution->setPublication($book);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The book has been updated.');

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return [
            'book' => $book,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Book entity.
     *
     * @Route("/{id}/delete", name="book_delete", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteAction(Request $request, Book $book) {
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
     *
     * @Template
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     */
    public function newContribution(Request $request, Book $book) {
        $contribution = new Contribution();

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contribution->setPublication($book);
            $em = $this->getDoctrine()->getManager();
            $em->persist($contribution);
            $em->flush();

            $this->addFlash('success', 'The new contribution was created.');

            return $this->redirectToRoute('book_show_contributions', ['id' => $book->getId()]);
        }

        return [
            'book' => $book,
            'form' => $form->createView(),
        ];
    }

    /**
     * Show book contributions list with edit/delete action items.
     *
     * @Route("/{id}/contributions", name="book_show_contributions")
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function showContributions(Book $book) {
        return [
            'book' => $book,
        ];
    }

    /**
     * Displays a form to edit an existing book Contribution entity.
     *
     * @Route("/contributions/{id}/edit", name="book_edit_contributions")
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function editContribution(Request $request, Contribution $contribution) {
        $editForm = $this->createForm(ContributionType::class, $contribution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The contribution has been updated.');

            return $this->redirectToRoute('book_show_contributions', ['id' => $contribution->getPublicationId()]);
        }

        return [
            'contribution' => $contribution,
            'edit_form' => $editForm->CreateView(),
        ];
    }

    /**
     * Deletes a book Contribution entity.
     *
     * @Route("/contributions/{id}/delete", name="book_delete_contributions")
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteContribution(Request $request, Contribution $contribution) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contribution);
        $em->flush();
        $this->addFlash('success', 'The contribution was deleted.');

        return $this->redirectToRoute('book_show_contributions', ['id' => $contribution->getPublicationId()]);
    }
}
