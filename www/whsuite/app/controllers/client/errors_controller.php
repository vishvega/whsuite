<?php

class ErrorsController extends ClientController
{
    public function index()
    {
        $this->pageNotFound();
    }

    /**
     * show a page not found page
     */
    public function pageNotFound()
    {
        $this->view->set('title', $this->lang->get('404_title'));
        $this->view->display('errors/pageNotFound.php');
    }
}
