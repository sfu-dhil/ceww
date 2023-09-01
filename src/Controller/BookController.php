<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Contribution;
use App\Entity\Publication;
use App\Form\BookType;
use App\Form\ContributionType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/book')]
class BookController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'book_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request, BookRepository $repo) : array|RedirectResponse {
        $pageSize = $this->getParameter('page_size');

        if ($request->query->has('alpha')) {
            $page = $repo->letterPage($request->query->get('alpha'), Publication::BOOK, $pageSize);

            return $this->redirectToRoute('book_index', ['page' => $page]);
        }
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Book::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();
        $books = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));
        $letterIndex = [];

        foreach ($books as $book) {
            $title = $book->getSortableTitle();
            if ( ! $title) {
                continue;
            }
            $letterIndex[mb_convert_case((string) $title[0], MB_CASE_UPPER)] = 1;
        }

        return [
            'books' => $books,
            'activeLetters' => array_keys($letterIndex),
        ];
    }

    #[Route(path: '/new', name: 'book_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($book->getContributions() as $contribution) {
                $contribution->setPublication($book);
            }

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

    #[Route(path: '/{id}', name: 'book_show', methods: ['GET'])]
    #[Template]
    public function show(BookRepository $bookRepository, Book $book) : array {
        return [
            'book' => $book,
            'next' => $bookRepository->next($book),
            'previous' => $bookRepository->previous($book),
        ];
    }

    #[Route(path: '/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Book $book) : array|RedirectResponse {
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
            $em->flush();
            $this->addFlash('success', 'The book has been updated.');

            return $this->redirectToRoute('book_show', ['id' => $book->getId()]);
        }

        return [
            'book' => $book,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Route(path: '/{id}/delete', name: 'book_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Book $book) : RedirectResponse {
        $em->remove($book);
        $em->flush();
        $this->addFlash('success', 'The book was deleted.');

        return $this->redirectToRoute('book_index');
    }

    #[Route(path: '/{id}/contributions/new', name: 'book_new_contribution')]
    #[Template]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    public function newContribution(EntityManagerInterface $em, Request $request, Book $book) : array|RedirectResponse {
        $contribution = new Contribution();

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contribution->setPublication($book);
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

    #[Route(path: '/{id}/contributions', name: 'book_show_contributions')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function showContributions(Book $book) : array {
        return [
            'book' => $book,
        ];
    }

    #[Route(path: '/contributions/{id}/edit', name: 'book_edit_contributions')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function editContribution(EntityManagerInterface $em, Request $request, Contribution $contribution) : array|RedirectResponse {
        $editForm = $this->createForm(ContributionType::class, $contribution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The contribution has been updated.');

            return $this->redirectToRoute('book_show_contributions', ['id' => $contribution->getPublicationId()]);
        }

        return [
            'contribution' => $contribution,
            'edit_form' => $editForm->CreateView(),
        ];
    }

    #[Route(path: '/contributions/{id}/delete', name: 'book_delete_contributions')]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function deleteContribution(EntityManagerInterface $em, Contribution $contribution) : RedirectResponse {
        $em->remove($contribution);
        $em->flush();
        $this->addFlash('success', 'The contribution was deleted.');

        return $this->redirectToRoute('book_show_contributions', ['id' => $contribution->getPublicationId()]);
    }
}
