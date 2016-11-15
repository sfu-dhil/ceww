<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
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
     * @Method({"GET", "POST"})
     * @Template()
     * @param Author $author
     */
    public function showAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Author');
        $statuses = $em->getRepository('AppBundle:Status')->findAll();
        
        $comments = $this->get('feedback.comment')->findComments($author);
        
        if($request->getMethod() === 'POST') {
            $status = $em->getRepository('AppBundle:Status')->find($request->request->get('status_id'));
            $author->setStatus($status);
            $em->flush();
            $this->addFlash('success', 'The author status has been updated');
            return $this->redirectToRoute('admin_author_show', array('id' => $author->getId()));
        }
        
        return array(
            'author' => $author,
            'comments' => $comments,
            'next' => $repo->next($author),
            'previous' => $repo->previous($author),
            'statuses' => $statuses,
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
            'q' => $q,
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
            'q' => $q,
            'author' => $author,
            'aliases' => $aliases,
        );
    }

    /**
     * Create a new residence for an author.
     *
     * @Route("/{id}/residence/new", name="admin_author_residence_new")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function newResidenceAction(Request $request, Author $author) {
        $residence = new Place();
        $form = $this->createForm('AppBundle\Form\PlaceType', $residence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($residence);
            $author->addResidence($residence);
            $em->flush();

            $this->addFlash('success', 'The new residence was created and added to ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        return array(
            'author' => $author,
            'residence' => $residence,
            'form' => $form->createView(),
        );
    }

    /**
     * Add residences to an author.
     *
     * @Route("/{id}/residence/add", name="admin_author_residence_add")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function addResidenceAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Place');
        $q = $request->query->get('q');

        if($request->getMethod() === 'POST') {
            $ids = $request->request->get('residence_id', array());
            foreach($ids as $id) {
                $residence = $repo->find($id);
                $author->addResidence($residence);
            }
            $em->flush();
            $this->addFlash('success', 'The residences have been added to ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        if($q) {
            $residences = $query = $repo->searchQuery($q)->execute();
        } else {
            $residences = array();
        }

        return array(
            'q' => $q,
            'author' => $author,
            'residences' => $residences,
        );
    }

    /**
     * Remove residences from an author.
     *
     * @Route("/{id}/residence/remove", name="admin_author_residence_remove")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function removeResidenceAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Place');
        $q = $request->query->get('q');

        if($request->getMethod() === 'POST') {
            $ids = $request->request->get('residence_id', array());
            foreach($ids as $id) {
                $residence = $repo->find($id);
                $author->removeResidence($residence);
            }
            $em->flush();
            $this->addFlash('success', 'The residences have been removed from ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        if($q) {
            $residences = $query = $repo->searchQuery($q)->execute();
        } else {
            $residences = array();
        }

        return array(
            'q' => $q,
            'author' => $author,
            'residences' => $residences,
        );
    }

    /**
     * Create a new publication for an author.
     *
     * @Route("/{id}/publication/new", name="admin_author_publication_new")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function newPublicationAction(Request $request, Author $author) {
        $publication = new Publication();
        $form = $this->createForm('AppBundle\Form\PublicationType', $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($publication);
            $author->addPublication($publication);
            $em->flush();

            $this->addFlash('success', 'The new publication was created and added to ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        return array(
            'author' => $author,
            'publication' => $publication,
            'form' => $form->createView(),
        );
    }

    /**
     * Add publications to an author.
     *
     * @Route("/{id}/publication/add", name="admin_author_publication_add")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function addPublicationAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Publication');
        $q = $request->query->get('q');

        if($request->getMethod() === 'POST') {
            $ids = $request->request->get('publication_id', array());
            foreach($ids as $id) {
                $publication = $repo->find($id);
                $author->addPublication($publication);
            }
            $em->flush();
            $this->addFlash('success', 'The publications have been added to ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        if($q) {
            $publications = $query = $repo->searchQuery($q)->execute();
        } else {
            $publications = array();
        }

        return array(
            'q' => $q,
            'author' => $author,
            'publications' => $publications,
        );
    }

    /**
     * Remove publications from an author.
     *
     * @Route("/{id}/publication/remove", name="admin_author_publication_remove")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     * @param Author  $author
     */
    public function removePublicationAction(Request $request, Author $author) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Publication');
        $q = $request->query->get('q');

        if($request->getMethod() === 'POST') {
            $ids = $request->request->get('publication_id', array());
            foreach($ids as $id) {
                $publication = $repo->find($id);
                $author->removePublication($publication);
            }
            $em->flush();
            $this->addFlash('success', 'The publications have been removed from ' . $author->getFullName());
            return $this->redirectToRoute('admin_author_show', array(
                'id' => $author->getId()
            ));
        }

        if($q) {
            $publications = $query = $repo->searchQuery($q)->execute();
        } else {
            $publications = array();
        }

        return array(
            'q' => $q,
            'author' => $author,
            'publications' => $publications,
        );
    }

}
