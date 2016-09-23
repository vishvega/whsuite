<?php
/**
 * Search Controller
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class SearchController extends AdminController
{
    /**
     * List Servers
     */
    public function results()
    {
        $title = $this->lang->get('search_results') . ': '. App::get('str')->limit(\Whsuite\Inputs\Post::get('q'), 50);
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $search_query = \Whsuite\Inputs\Post::get('q');

        $this->view->set('client_results', $this->clientResults($search_query));
        $this->view->set('domain_results', $this->domainResults($search_query));

        $this->view->set('search_query', $search_query);
        $this->view->display('search/results.php');
    }

    private function clientResults($term, $page = 1)
    {
        $conditions = array(
            array(
                'column' => 'first_name',
                'operator' => 'LIKE',
                'value' => '%' . $term . '%'
            ),
            array(
                'type' => 'or',
                'column' => 'last_name',
                'operator' => 'LIKE',
                'value' => '%' . $term . '%'
            ),
            array(
                'type' => 'or',
                'column' => 'email',
                'operator' => 'LIKE',
                'value' => '%' . $term . '%'
            ),
            array(
                'type' => 'or',
                'column' => 'company',
                'operator' => 'LIKE',
                'value' => '%' . $term . '%'
            ),
        );

        $results = Client::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'id', 'desc', 'admin-search');

        return $results;
    }

    private function domainResults($term, $page = 1)
    {
        $conditions = array(
            array(
                'column' => 'domain',
                'operator' => 'LIKE',
                'value' => '%' . $term . '%'
            )
        );

        $results = Domain::paginate(App::get('configs')->get('settings.general.results_per_page'), $page, $conditions, 'id', 'desc', 'admin-search-paging');

        return $results;
    }
}