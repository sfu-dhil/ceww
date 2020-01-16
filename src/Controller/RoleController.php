<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\PersonRepository;
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
class RoleController extends AbstractController {
    /**
     * Lists all Role entities.
     *
     * @Route("/", name="role_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Role::class, 'e')->orderBy('e.label', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $roles = $paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return array(
            'roles' => $roles,
        );
    }

    /**
     * Creates a new Role entity.
     *
     * @Route("/new", name="role_new", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     * @Template()
     *
     * @param Request $request
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

            return $this->redirectToRoute('role_show', array('id' => $role->getId()));
        }

        return array(
            'role' => $role,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Role entity.
     *
     * @Route("/{id}", name="role_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     * @param Role $role
     * @param PersonRepository $repo
     */
    public function showAction(Request $request, Role $role, PersonRepository $repo) {
        $query = $repo->byRoleQuery($role);
        $paginator = $this->get('knp_paginator');
        $people = $paginator->paginate($query, $request->query->getint('page', 1), $this->getParameter('page_size'));

        return array(
            'role' => $role,
            'people' => $people,
        );
    }

    /**
     * Displays a form to edit an existing Role entity.
     *
     * @Route("/{id}/edit", name="role_edit", methods={"GET","POST"})
     *
     * @Template()
     * @Security("is_granted('ROLE_CONTENT_EDITOR')")
     *
     * @param Request $request
     * @param Role $role
     */
    public function editAction(Request $request, Role $role) {
        $editForm = $this->createForm(RoleType::class, $role);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The role has been updated.');

            return $this->redirectToRoute('role_show', array('id' => $role->getId()));
        }

        return array(
            'role' => $role,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Role entity.
     *
     * @Route("/{id}/delete", name="role_delete", methods={"GET","POST"})
     *
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     *
     * @param Request $request
     * @param Role $role
     */
    public function deleteAction(Request $request, Role $role) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($role);
        $em->flush();
        $this->addFlash('success', 'The role was deleted.');

        return $this->redirectToRoute('role_index');
    }
}
