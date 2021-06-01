<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Place;
use App\Entity\Publisher;
use App\Form\PublisherType;
use App\Index\PublisherIndex;
use App\Repository\PersonRepository;
use App\Repository\PublisherRepository;
use App\Services\Merger;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\SolrBundle\Services\SolrManager;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Publisher controller.
 *
 * @Route("/publisher")
 */
class PublisherController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Publisher entities.
     *
     * @return array
     *
     * @Route("/", name="publisher_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Publisher::class, 'e')->orderBy('e.name', 'ASC');
        $query = $qb->getQuery();

        $publishers = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'publishers' => $publishers,
        ];
    }

    /**
     * Typeahead API endpoint for Publisher entities.
     *
     * @Route("/typeahead", name="publisher_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, PublisherRepository $repo) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Search for Publisher entities.
     *
     * @Route("/search", name="publisher_search", methods={"GET"})
     *
     * @Template
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Publisher::class);
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $publishers = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $publishers = [];
        }

        return [
            'publishers' => $publishers,
            'q' => $q,
        ];
    }

    /**
     * @Route("/solr", name="publisher_solr")
     * @Template
     */
    public function solrAction(Request $request, PublisherIndex $repo, SolrManager $solr) {
        $q = $request->query->get('q');
        $result = null;
        if ($q) {
            $filters = $request->query->get('filter', []);

            $order = null;
            $m = [];
            if (preg_match('/^(\\w+).(asc|desc)$/', $request->query->get('order', 'score.desc'), $m)) {
                $order = [$m[1] => $m[2]];
            }

            $query = $repo->searchQuery($q, $filters, $order);
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
     * Creates a new Publisher entity.
     *
     * @return array|Response
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Route("/new", name="publisher_new", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newAction(Request $request) {
        $publisher = new Publisher();
        $form = $this->createForm(PublisherType::class, $publisher);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publisher);
            $em->flush();

            $this->addFlash('success', 'The new publisher was created.');

            return $this->redirectToRoute('publisher_show', ['id' => $publisher->getId()]);
        }

        return [
            'publisher' => $publisher,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Publisher entity in a popup.
     *
     * @return array|Response
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Route("/new_popup", name="publisher_new_popup", methods={"GET", "POST"})
     *
     * @Template
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Publisher entity.
     *
     * @return array
     *
     * @Route("/{id}", name="publisher_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Publisher $publisher, PersonRepository $repo) {
        return [
            'publisher' => $publisher,
            'people' => $repo->byPublisher($publisher),
        ];
    }

    /**
     * Displays a form to edit an existing Publisher entity.
     *
     * @return array|Response
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Route("/{id}/edit", name="publisher_edit", methods={"GET", "POST"})
     *
     * @Template
     */
    public function editAction(Request $request, Publisher $publisher) {
        $editForm = $this->createForm(PublisherType::class, $publisher);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The publisher has been updated.');

            return $this->redirectToRoute('publisher_show', ['id' => $publisher->getId()]);
        }

        return [
            'publisher' => $publisher,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Finds and displays a Place entity.
     *
     * @Route("/{id}/merge", name="publisher_merge")
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Template
     */
    public function mergeAction(Request $request, Publisher $publisher, Merger $merger, PublisherRepository $repo) {
        if ('POST' === $request->getMethod()) {
            $publishers = $repo->findBy(['id' => $request->request->get('publishers')]);
            $count = count($publishers);
            $merger->publishers($publisher, $publishers);
            $this->addFlash('success', "Merged {$count} publishers into {$publisher->getName()}.");

            return $this->redirect($this->generateUrl('publisher_show', ['id' => $publisher->getId()]));
        }

        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $publishers = $query->execute();
        } else {
            $publishers = [];
        }

        return [
            'publisher' => $publisher,
            'publishers' => $publishers,
            'q' => $q,
        ];
    }

    /**
     * Deletes a Publisher entity.
     *
     * @param Request $request Dependency injected HTTP request object.
     * @param Publisher $publisher The Publisher to delete.
     *
     * @return array|Response A redirect to the publisher_index.
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="publisher_delete", methods={"GET", "POST"})
     */
    public function deleteAction(Request $request, Publisher $publisher) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($publisher);
        $em->flush();
        $this->addFlash('success', 'The publisher was deleted.');

        return $this->redirectToRoute('publisher_index');
    }
}
