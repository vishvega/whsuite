<?php

class BillingPeriodsController extends AdminController
{

   /**
     * scaffolding overrides for group listing
     *
     * see admin base controller for doc blocks
     */
    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-billingperiod',
                'link_class' => '',
                'icon' => 'fa fa-user',
                'label' => 'billingperiod_management'
            ),
            array(
                'url_route' => 'admin-billingperiod-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'billingperiod_add'
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
                'field' => 'days'
            ),
            array(
                'field' => 'updated_at'
            ),
            array(
                'action' => 'edit',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'edit' => array(
                'url_route' => 'admin-billingperiod-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            )
        );
    }

    protected function formFields()
    {
        $fields = array(
            'BillingPeriod.id',
            'BillingPeriod.name',
            'BillingPeriod.days',
            'BillingPeriod.sort'
        );

        return $fields;
    }

}
