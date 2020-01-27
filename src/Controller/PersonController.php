<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\AliasRepository;
use App\Repository\PersonRepository;
use App\Repository\PublisherRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Person controller.
 *
 * @Route("/person")
 */
class PersonController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Person entities.
     *
     * @Route("/", name="person_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Person::class, 'e');
        if ( ! $this->isGranted('ROLE_USER')) {
            $qb->where("e.gender <> 'm'");
            $qb->andWhere('e.canadian is null OR e.canadian != 0');
        }
        $qb->orderBy('e.sortableName');
        $query = $qb->getQuery();

        $people = $this->paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return [
            'people' => $people,
        ];
    }

    /**
     * @Route("/pageinfo", name="person_pageinfo")
     *
     * @return JsonResponse
     */
    public function pageInfoAction(Request $request, PersonRepository $repo) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $people = $repo->pageInfoQuery($q, $this->getParameter('page_size')); // should be first person on page, last person on page.
        $data = [
            'first' => ($people['first'] ? [
                'name' => $people['first']->getFullname(),
                'id' => $people['first']->getId(),
            ] : null),
            'last' => ($people['last'] ? [
                'name' => $people['last']->getFullname(),
                'id' => $people['last']->getId(),
            ] : null),
            'pages' => ceil($people['total'] / $this->getParameter('page_size')),
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/typeahead", name="person_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeaheadAction(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Person');
        $data = [];
        foreach ($repo->typeaheadQuery($q) as $result) {
            $name = $result->getFullname();
            if (count($result->getAliases())) {
                $name .= ' aka ' . $result->getAliases()->first();
            }
            $data[] = [
                'id' => $result->getId(),
                'text' => $name,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Search for Person entities.
     *
     * @Route("/search", name="person_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function searchAction(Request $request, PersonRepository $personRepo, AliasRepository $aliasRepo) {
        $q = $request->query->get('q');
        if ($q) {
            $personQuery = $personRepo->searchQuery($q);
            $aliasQuery = $aliasRepo->searchQuery($q);

            $people = $this->paginator->paginate($personQuery, $request->query->getInt('page', 1), $this->getParameter('page_size'));
            $aliases = $this->paginator->paginate($aliasQuery, $request->query->getInt('page', 1), $this->getParameter('page_size'));
        } else {
            $people = [];
            $aliases = [];
        }

        return [
            'people' => $people,
            'aliases' => $aliases,
            'q' => $q,
            'page' => $request->query->getInt('page', 1),
        ];
    }

    /**
     * Creates a new Person entity.
     *
     * @Route("/new", name="person_new", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     */
    public function newAction(Request $request) {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            $this->addFlash('success', 'The new person was created.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'form' => $form->createView(),
        ];
    }

    /**
     * Creates a new Person entity in a popup.
     *
     * @Route("/new_popup", name="person_new_popup", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     */
    public function newPopupAction(Request $request) {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Person entity.
     *
     * @Route("/{id}", name="person_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Person $person, PersonRepository $repo, PublisherRepository $publisherRepo) {
        if ( ! $this->isGranted('ROLE_USER') && Person::FEMALE !== $person->getGender()) {
            throw new NotFoundHttpException('Cannot find that person.');
        }

        return [
            'person' => $person,
            'next' => $repo->next($person),
            'previous' => $repo->previous($person),
            'publishers' => $publisherRepo->byPerson($person),
        ];
    }

    /**
     * Displays a form to edit an existing Person entity.
     *
     * @Route("/{id}/edit", name="person_edit", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     */
    public function editAction(Request $request, Person $person) {
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->flush();
            $this->addFlash('success', 'The person has been updated.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Person entity.
     *
     * @Route("/{id}/delete", name="person_delete", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteAction(Request $request, Person $person) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();
        $this->addFlash('success', 'The person was deleted.');

        return $this->redirectToRoute('person_index');
    }
}
