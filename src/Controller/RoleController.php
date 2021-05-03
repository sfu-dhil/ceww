<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\PersonRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Role controller.
 *
 * @Route("/role")
 */
class RoleController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Role entities.
     *
     * @Route("/", name="role_index", methods={"GET"})
     *
     * @Template
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Role::class, 'e')->orderBy('e.label', 'ASC');
        $query = $qb->getQuery();

        $roles = $this->paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return [
            'roles' => $roles,
        ];
    }

    /**
     * Creates a new Role entity.
     *
     * @Route("/new", name="role_new", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template
     */
    public function newAction(Request $request) {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $this->addFlash('success', 'The new role was created.');

            return $this->redirectToRoute('role_show', ['id' => $role->getId()]);
        }

        return [
            'role' => $role,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Role entity.
     *
     * @Route("/{id}", name="role_show", methods={"GET"})
     *
     * @Template
     */
    public function showAction(Request $request, Role $role, PersonRepository $repo) {
        $query = $repo->byRoleQuery($role);

        $people = $this->paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return [
            'role' => $role,
            'people' => $people,
        ];
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/{id}/edit", name="role_edit", methods={"GET", "POST"})
     *
     * @Template
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     */
    public function editAction(Request $request, Role $role) {
        $editForm = $this->createForm(RoleType::class, $role);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The role has been updated.');

            return $this->redirectToRoute('role_show', ['id' => $role->getId()]);
        }

        return [
            'role' => $role,
            'edit_form' => $editForm->createView(),
        ];
    }

    /**
     * Deletes a Role entity.
     *
     * @Route("/{id}/delete", name="role_delete", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     */
    public function deleteAction(Request $request, Role $role) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($role);
        $em->flush();
        $this->addFlash('success', 'The role was deleted.');

        return $this->redirectToRoute('role_index');
    }
}
