<?php

class TaxLevelsController extends AdminController
{

    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-taxlevel',
                'link_class' => '',
                'icon' => 'fa fa-user',
                'label' => 'taxlevel_management'
            ),
            array(
                'url_route' => 'admin-taxlevel-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'taxlevel_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'name'
            ),
            array(
                'field' => 'state'
            ),
            array(
                'field' => 'country',
            ),
            array(
                'field' => 'updated_at'
            ),
            array(
                'action' => 'edit',
                'label' => null
            ),
            array(
                'action' => 'delete',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'edit' => array(
                'url_route' => 'admin-taxlevel-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-taxlevel-delete',
                'link_class' => 'btn btn-danger btn-small pull-right',
                'icon' => 'fa fa-remove',
                'label' => 'delete',
                'params' => array('id')
            )
        );
    }

    protected function formFields()
    {
        $fields = array(
            'TaxLevel.id',
            'TaxLevel.name',
            'TaxLevel.level' => array(
                'type' => 'select'
            ),
            'TaxLevel.rate',
            'TaxLevel.state',
            'TaxLevel.country' => array(
                'type' => 'select'
            )
        );

        return $fields;
    }

    protected function getExtraData($model)
    {
        $level = array(
            '1' => $this->lang->get('level_1_tax'),
            '2' => $this->lang->get('level_2_tax')
        );

        $this->view->set('level', $level);

        $this->view->set('country', Country::getCountries(true));
    }
}
