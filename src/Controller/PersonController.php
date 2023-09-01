<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use App\Repository\PublisherRepository;
use App\Services\ElasticSearchHelper;
use Doctrine\ORM\EntityManagerInterface;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/person')]
class PersonController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    public function __construct(
        private PaginatedFinderInterface $finder,
    ) {}

    #[Route(path: '/', name: 'person_index', methods: ['GET'])]
    #[Template]
    public function index(EntityManagerInterface $em, Request $request) : array {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Person::class, 'e');
        if ( ! $this->isGranted('ROLE_USER')) {
            $qb->where("e.gender <> 'm'")
                ->andWhere('e.canadian is null OR e.canadian != 0')
            ;
        }
        $qb->orderBy('e.sortableName');
        $query = $qb->getQuery();

        $people = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));

        return [
            'people' => $people,
        ];
    }

    #[Route(path: '/pageinfo', name: 'person_pageinfo')]
    public function pageInfo(Request $request, PersonRepository $repo) : JsonResponse {
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

    #[Route(path: '/typeahead', name: 'person_typeahead', methods: ['GET'])]
    public function typeahead(PersonRepository $personRepository, Request $request) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($personRepository->typeaheadQuery($q) as $result) {
            $name = $result->getFullname();
            if (is_countable($result->getAliases()) ? count($result->getAliases()) : 0) {
                $name .= ' aka ' . $result->getAliases()->first();
            }
            $data[] = [
                'id' => $result->getId(),
                'text' => $name,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/search', name: 'person_search')]
    #[Template]
    public function search(Request $request) : array {
        $elasticSearchHelper = new ElasticSearchHelper([
            'queryTermFields' => [
                'fullName^2.5',
                'description^0.5',
                'birthPlace.name^0.4',
                'deathPlace.name^0.4',
                'residences.name^0.3',
                'aliases.name^1.3',
            ],
            'rangeFilters' => [
                'birthDate' => ElasticSearchHelper::generateRangeFilter('birthDate.year', 1750, (int) date('Y'), 50),
                'deathDate' => ElasticSearchHelper::generateRangeFilter('deathDate.year', 1750, (int) date('Y'), 50),
            ],
            'sort' => ElasticSearchHelper::generateDefaultSortOrder(),
            'highlights' => [
                'fullName' => new stdClass(),
                'description' => new stdClass(),
                'birthPlace.name' => new stdClass(),
                'deathPlace.name' => new stdClass(),
                'residences.name' => new stdClass(),
                'aliases.name' => new stdClass(),
            ],
        ]);
        $query = $elasticSearchHelper->getElasticQuery(
            $request->query->get('q'),
            $request->query->get('order'),
            $request->query->all('filters')
        );
        $results = $this->finder->createHybridPaginatorAdapter($query);

        return [
            'results' => $this->paginator->paginate($results, $request->query->getInt('page', 1), $this->getParameter('page_size')),
            'sortOptions' => ElasticSearchHelper::generateDefaultSortOrder(),
        ];
    }

    #[Route(path: '/new', name: 'person_new', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function new(EntityManagerInterface $em, Request $request) : array|RedirectResponse {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    #[Route(path: '/{id}', name: 'person_show', methods: ['GET'])]
    #[Template]
    public function show(Person $person, PersonRepository $repo, PublisherRepository $publisherRepo) : array|RedirectResponse {
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

    #[Route(path: '/{id}/edit', name: 'person_edit', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_EDITOR')")]
    #[Template]
    public function edit(EntityManagerInterface $em, Request $request, Person $person) : array|RedirectResponse {
        $editForm = $this->createForm(PersonType::class, $person);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'The person has been updated.');

            return $this->redirectToRoute('person_show', ['id' => $person->getId()]);
        }

        return [
            'person' => $person,
            'edit_form' => $editForm->createView(),
        ];
    }

    #[Route(path: '/{id}/delete', name: 'person_delete', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_CONTENT_ADMIN')")]
    public function delete(EntityManagerInterface $em, Person $person) : RedirectResponse {
        $em->remove($person);
        $em->flush();
        $this->addFlash('success', 'The person was deleted.');

        return $this->redirectToRoute('person_index');
    }
}
