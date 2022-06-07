<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Publication;
use App\Index\DefaultIndex;
use App\Index\PublicationIndex;
use App\Repository\PublicationRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\SolrBundle\Services\SolrManager;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="homepage")
     * @Template
     *
     * @return array
     */
    public function indexAction(Request $request) {
        return [];
    }

    /**
     * Search for publication entities.
     *
     * @Route("/search", name="search")
     *
     * @Template
     *
     * @return array
     */
    public function searchAction(Request $request, PublicationRepository $repo) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $publications = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'));
        } else {
            $publications = [];
        }

        return [
            'publications' => $publications,
            'q' => $q,
        ];
    }

    /**
     * @Route("/solr", name="solr")
     * @Template
     */
    public function solrAction(Request $request, DefaultIndex $repo, SolrManager $solr) {
        $q = $request->query->get('q');
        $result = null;
        if ($q) {
            $order = null;
            $filters = $request->query->get('filter', []);
            $m = [];
            if (preg_match('/^(\\w+).(asc|desc)$/', $request->query->get('order', 'score.desc'), $m)) {
                $order = [$m[1] => $m[2]];
            } else {
                $order = ['score' => 'desc'];
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
     * @Route("/solr_title", name="solr_title")
     * @Template
     */
    public function solrTitleAction(Request $request, PublicationIndex $index, SolrManager $solr) {
        $q = $request->query->get('q');
        $result = null;
        if ($q) {
            $order = null;
            $filters = $request->query->get('filter', []);
            $rangeFilters = $request->query->get('filter_range', []);

            $m = [];
            if (preg_match('/^(\\w+).(asc|desc)$/', $request->query->get('order', 'score.desc'), $m)) {
                $order = [$m[1] => $m[2]];
            } else {
                $order = ['score' => 'desc'];
            }

            $query = $index->searchQuery($q, $filters, $rangeFilters, $order);
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
     * @Route("/privacy", name="privacy")
     * @Template
     */
    public function privacyAction(Request $request) : void {
    }
}
