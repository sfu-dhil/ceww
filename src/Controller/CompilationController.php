<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Compilation;
use App\Entity\Contribution;
use App\Entity\Publication;
use App\Form\CompilationType;
use App\Form\ContributionType;
use App\Repository\CompilationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Compilation controller.
 */
#[Route(path: '/compilation')]
class CompilationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'compilation_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request, CompilationRepository $repo) : array|RedirectResponse {
        $pageSize = $this->getParameter('page_size');

        if ($request->query->has('alpha')) {
            $page = $repo->letterPage($request->query->get('alpha'), Publication::COMPILATION, $pageSize);

            return $this->redirectToRoute('compilation_index', ['page' => $page]);
        }

        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Compilation::class, 'e')->orderBy('e.sortableTitle', 'ASC');
        $query = $qb->getQuery();

        $compilations = $this->paginator->paginate($query, $request->query->getInt('page', 1), $pageSize);

        $letterIndex = [];

        foreach ($compilations as $compilation) {
            $title = $compilation->getSortableTitle();
            if ( ! $title) {
                continue;
            }
            $letterIndex[mb_convert_case((string) $title[0], MB_CASE_UPPER)] = 1;
        }

        return [
            'compilations' => $compilations,
            'activeLetters' => array_keys($letterIndex),
        ];
    }

    #[Route(path: '/new', name: 'compilation_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $compilation = new Compilation();
        $form = $this->createForm(CompilationType::class, $compilation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($compilation->getContributions() as $contribution) {
                $contribution->setPublication($compilation);
            }
            $em->persist($compilation);
            $em->flush();

            $this->addFlash('success', 'The new collection was created.');

            return $this->redirectToRoute('compilation_show', ['id' => $compilation->getId()]);
        }

        return [
            'compilation' => $compilation,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'compilation_show', methods: ['GET'])]
    #[Template]
    public function show(CompilationRepository $compilationRepository, Compilation $compilation) : array {
        return [
            'compilation' => $compilation,
            'next' => $compilationRepository->next($compilation),
            'previous' => $compilationRepository->previous($compilation),
        ];
    }

    #[Route(path: '/{id}/edit', name: 'compilation_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Compilation $compilation) : array|RedirectResponse {
        $editForm = $this->createForm(CompilationType::class, $compilation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($compilation->getContributions() as $contribution) {
                $contribution->setPublication($compilation);
            }
            $em->flush();
            $this->addFlash('success', 'The collection has been updated.');

            return $this->redirectToRoute('compilation_show', ['id' => $compilation->getId()]);
        }

        return [
            'compilation' => $compilation,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Route(path: '/{id}/delete', name: 'compilation_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Compilation $compilation) : RedirectResponse {
        $em->remove($compilation);
        $em->flush();
        $this->addFlash('success', 'The compilation was deleted.');

        return $this->redirectToRoute('compilation_index');
    }

    #[Route(path: '/{id}/contributions/new', name: 'compilation_new_contribution')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function newContribution(EntityManagerInterface $em, Request $request, Compilation $compilation) : array|RedirectResponse {
        $contribution = new Contribution();

        $form = $this->createForm(ContributionType::class, $contribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contribution->setPublication($compilation);
            $em->persist($contribution);
            $em->flush();

            $this->addFlash('success', 'The new contribution was created.');

            return $this->redirectToRoute('compilation_show_contributions', ['id' => $compilation->getId()]);
        }

        return [
            'compilation' => $compilation,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}/contributions', name: 'compilation_show_contributions')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function showContributions(Compilation $compilation) : array {
        return [
            'compilation' => $compilation,
        ];
    }

    #[Route(path: '/contributions/{id}/edit', name: 'compilation_edit_contributions')]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function editContribution(EntityManagerInterface $em, Request $request, Contribution $contribution) : array|RedirectResponse {
        $editForm = $this->createForm(ContributionType::class, $contribution);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The contribution has been updated.');

            return $this->redirectToRoute('compilation_show_contributions', ['id' => $contribution->getPublicationId()]);
        }

        return [
            'contribution' => $contribution,
            'edit_form' => $editForm->CreateView(),
        ];
    }

    #[Route(path: '/contributions/{id}/delete', name: 'compilation_delete_contributions')]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function deleteContribution(EntityManagerInterface $em, Contribution $contribution) : RedirectResponse {
        $em->remove($contribution);
        $em->flush();
        $this->addFlash('success', 'The contribution was deleted.');

        return $this->redirectToRoute('compilation_show_contributions', ['id' => $contribution->getPublicationId()]);
    }
}
