<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Author controller.
 *
 * @Route("/admin/author")
 */
class AuthorController extends Controller {

    /**
     * Lists all Author entities.
     *
     * @Route("/", name="admin_author_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Author e ORDER BY e.sortableName';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $authors = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'authors' => $authors,
        );
    }

    /**
     * Search for Author entities.
     *
     * @Route("/search", name="admin_author_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Author');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $authors = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $authors = array();
        }

        return array(
            'authors' => $authors,
            'q' => $q,
        );
    }

    /**
     * Full text search for Author entities.
     *
     * Requires a MatchAgainst function be added to doctrine, and appropriate
     * fulltext indexes on your Author entity.
     *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
     *
     *
     * @Route("/fulltext", name="admin_author_fulltext")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function fulltextAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Author');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->fulltextQuery($q);
            $paginator = $this->get('knp_paginator');
            $authors = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        } else {
            $authors = array();
        }

        return array(
            'authors' => $authors,
            'q' => $q,
        );
    }

    /**
     * Creates a new Author entity.
     *
     * @Route("/new", name="admin_author_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $author = new Author();
        $form = $this->createForm('AppBundle\Form\AuthorType', $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($author);
            $em->flush();

            $this->addFlash('success', 'The new author was created.');
            return $this->redirectToRoute('admin_author_show', array('id' => $author->getId()));
        }

        return array(
            'author' => $author,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Author entity.
     *
     * @Route("/{id}", name="admin_author_show")
     * @Method("GET")
     * @Template()
     * @param Author $author
     */
    public function showAction(Author $author) {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Author');

        return array(
            'author' => $author,
            'next' => $repo->next($author),
            'previous' => $repo->previous($author),
        );
    }

    /**
     * Displays a form to edit an existing Author entity.
     *
     * @Route("/{id}/edit", name="admin_author_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function editAction(Request $request, Author $author) {
        $editForm = $this->createForm('AppBundle\Form\AuthorType', $author);
        if ($author->getBirthPlace()) {
            $editForm['birthplace']->setData($author->getBirthPlace()->getName());
            $editForm['birthplace_id']->setData($author->getBirthPlace()->getId());
        }
        if ($author->getDeathPlace()) {
            $editForm['deathplace']->setData($author->getDeathPlace()->getName());
            $editForm['deathplace_id']->setData($author->getDeathPlace()->getId());
        }

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $birthPlaceId = $editForm['birthplace_id']->getData();
            $author->setBirthPlace($em->find('AppBundle:Place', $birthPlaceId));

            $deathPlaceId = $editForm['deathplace_id']->getData();
            $author->setDeathPlace($em->find('AppBundle:Place', $deathPlaceId));

            $em->flush();
            $this->addFlash('success', 'The author has been updated.');
            return $this->redirectToRoute('admin_author_show', array('id' => $author->getId()));
        }

        return array(
            'author' => $author,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Author entity.
     *
     * @Route("/{id}/delete", name="admin_author_delete")
     * @Method("GET")
     * @param Request $request
     * @param Author  $author
     */
    public function deleteAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();
        $this->addFlash('success', 'The author was deleted.');

        return $this->redirectToRoute('admin_author_index');
    }

    /**
     * Create a new alias for an author.
     *
     * @Route("/{id}/alias/new", name="admin_author_alias_new")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function newAliasAction(Request $request, Author $author) {
        $alias = new Alias();
        $form = $this->createForm('AppBundle\Form\AliasType', $alias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($alias);
            $author->addAlias($alias);
            $em->flush();

            $this->addFlash('success', 'The new alias was created and added to ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        return array(
            'author' => $author,
            'alias' => $alias,
            'form' => $form->createView(),
        );
    }

    /**
     * Add aliases to an author.
     *
     * @Route("/{id}/alias/add", name="admin_author_alias_add")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function addAliasAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Alias');
        $q = $request->query->get('q');

        if($request->getMethod() === 'POST') {
            $ids = $request->request->get('alias_id', array());
            foreach($ids as $id) {
                $alias = $repo->find($id);
                $author->addAlias($alias);
            }
            $em->flush();
            $this->addFlash('success', 'The aliases have been added to ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        if($q) {
            $aliases = $query = $repo->searchQuery($q)->execute();
        } else {
            $aliases = array();
        }

        return array(
            'q' => '',
            'author' => $author,
            'aliases' => $aliases,
        );
    }

    /**
     * Remove aliases from an author.
     *
     * @Route("/{id}/alias/remove", name="admin_author_alias_remove")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function removeAliasAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Alias');
        $q = $request->query->get('q');

        if($request->getMethod() === 'POST') {
            $ids = $request->request->get('alias_id', array());
            foreach($ids as $id) {
                $alias = $repo->find($id);
                $author->removeAlias($alias);
            }
            $em->flush();
            $this->addFlash('success', 'The aliases have been removed from ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        if($q) {
            $aliases = $query = $repo->searchQuery($q)->execute();
        } else {
            $aliases = array();
        }

        return array(
            'q' => '',
            'author' => $author,
            'aliases' => $aliases,
        );
    }

}
