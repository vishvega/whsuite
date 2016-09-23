<?php

class DomainExtensionsController extends AdminController
{
    /**
     * scaffolding overrides for domain extensions
     *
     * see admin base controller for doc blocks
     */
    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-domainextension',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'domainextension_management'
            ),
            array(
                'url_route' => 'admin-domainextension-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'domainextension_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'extension'
            ),
            array(
                'field' => 'Registrar.name',
                'label' => 'registrar'
            ),
            array(
                'field' => 'Product.name',
                'label' => 'product'
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
                'url_route' => 'admin-domainextension-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-domainextension-delete',
                'link_class' => 'btn btn-danger btn-small pull-right',
                'icon' => 'fa fa-remove',
                'label' => 'delete',
                'params' => array('id')
            )
        );
    }

    protected function formFields()
    {
        return array(
            'DomainExtension.id',
            'DomainExtension.extension',
            'DomainExtension.registrar_id' => array(
                'type' => 'select',
                'label' => 'registrar'
            ),
            'DomainExtension.automatic_registration' => array(
                'type' => 'checkbox'
            ),
            'DomainExtension.sort',
            'DomainExtension.has_eppcode',
            'DomainExtension.min_years' => array(
                'type' => 'select'
            ),
            'DomainExtension.max_years' => array(
                'type' => 'select'
            )
        );
    }

    protected function getExtraData($model)
    {
        $registrars = Registrar::formattedList('id', 'name', array(), 'name', 'asc');

        $min_years = array();

        for ($i = 1; $i <= 100; $i++) {

            $min_years[$i] = $i . ' Year';
            if ($i > 1) {

                $min_years[$i] .= 's';
            }
        }
        $max_years = $min_years;

        $this->view->set(array(
            'registrar_id' => $registrars,
            'min_years' => $min_years,
            'max_years' => $max_years
        ));
    }

}
