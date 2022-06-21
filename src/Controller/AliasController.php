<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Alias;
use App\Form\AliasType;
use App\Index\AliasIndex;
use App\Repository\AliasRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\SolrBundle\Services\SolrManager;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Alias controller.
 *
 * @Route("/alias")
 */
class AliasController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Alias entities.
     *
     * @Route("/", name="alias_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Alias::class, 'e')->orderBy('e.sortableName', 'ASC');
        $query = $qb->getQuery();

        $aliases = $this->paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return [
            'aliases' => $aliases,
        ];
    }

    /**
     * Search for Alias entities.
     *
     * @Route("/search", name="alias_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function searchAction(Request $request, AliasIndex $index, SolrManager $solr) {
        $q = $request->query->get('q');
        $result = null;
        if ($q) {
            $filters = $request->query->get('filter', []);

            $order = null;
            $m = [];
            if (preg_match('/^(\\w+).(asc|desc)$/', $request->query->get('order', 'score.desc'), $m)) {
                $order = [$m[1] => $m[2]];
            }

            $query = $index->searchQuery($q, $filters, $order);
            $result = $solr->execute($query, $this->paginator, [
                'page' => (int) $request->query->get('page', 1),
                'pageSize' => (int) $this->getParameter('page_size'),
            ]);
        }

        return [
            'q' => $q,
            'result' => $result,
        ];
    }

    /**
     * @Route("/typeahead", name="alias_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, AliasRepository $repo) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => $result->getName(),
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Creates a new Alias entity.
     *
     * @Route("/new", name="alias_new", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function newAction(Request $request) {
        $alias = new Alias();
        $form = $this->createForm(AliasType::class, $alias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alias);
            $em->flush();

            $this->addFlash('success', 'The new alias was created.');

            return $this->redirectToRoute('alias_show', ['id' => $alias->getId()]);
        }

        return [
            'alias' => $alias,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Alias entity.
     *
     * @Route("/new", name="alias_new_popup", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Alias entity.
     *
     * @Route("/{id}", name="alias_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Alias $alias) {
        return [
            'alias' => $alias,
        ];
    }

    /**
     * Displays a form to edit an existing Alias entity.
     *
     * @Route("/{id}/edit", name="alias_edit", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function editAction(Request $request, Alias $alias) {
        $editForm = $this->createForm(AliasType::class, $alias);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The alias has been updated.');

            return $this->redirectToRoute('alias_show', ['id' => $alias->getId()]);
        }

        return [
            'alias' => $alias,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Alias entity.
     *
     * @Route("/{id}/delete", name="alias_delete", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteAction(Request $request, Alias $alias) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($alias);
        $em->flush();
        $this->addFlash('success', 'The alias was deleted.');

        return $this->redirectToRoute('alias_index');
    }
}
