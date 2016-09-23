<?php

class ErrorsController extends AdminController
{
    public function index()
    {
        $this->view->set('title', $this->lang->get('404_title'));
        $this->view->display('errors/index.php');
    }
}
