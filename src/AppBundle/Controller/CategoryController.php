<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;

/**
 * Category controller.
 *
 * @Route("/admin/category")
 */
class CategoryController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/", name="admin_category_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Category e ORDER BY e.label';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $categories = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'categories' => $categories,
        );
    }

    /**
     * Search for Category entities.
     *
     * To make this work, add a method like this one to the
     * AppBundle:Category repository. Replace the fieldName with
     * something appropriate, and adjust the generated search.html.twig
     * template.
     *
      //    public function searchQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->where("e.fieldName like '%$q%'");
      //        return $qb->getQuery();
      //    }
     *
     *
     * @Route("/search", name="admin_category_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Category');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $categories = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $categories = array();
        }

        return array(
            'categories' => $categories,
            'q' => $q,
        );
    }

    /**
     * Creates a new Category entity.
     *
     * @Route("/new", name="admin_category_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm('AppBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'The new category was created.');
            return $this->redirectToRoute('admin_category_show', array('id' => $category->getId()));
        }

        return array(
            'category' => $category,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Category entity.
     *
     * @Route("/{id}", name="admin_category_show")
     * @Method("GET")
     * @Template()
     * @param Category $category
     */
    public function showAction(Category $category)
    {

        return array(
            'category' => $category,
        );
    }

    /**
     * Displays a form to edit an existing Category entity.
     *
     * @Route("/{id}/edit", name="admin_category_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Category $category
     */
    public function editAction(Request $request, Category $category)
    {
        $editForm = $this->createForm('AppBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'The category has been updated.');
            return $this->redirectToRoute('admin_category_show', array('id' => $category->getId()));
        }

        return array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/{id}/delete", name="admin_category_delete")
     * @Method("GET")
     * @param Request $request
     * @param Category $category
     */
    public function deleteAction(Request $request, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'The category was deleted.');

        return $this->redirectToRoute('admin_category_index');
    }
}
