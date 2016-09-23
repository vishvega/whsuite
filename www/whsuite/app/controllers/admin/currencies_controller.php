<?php

use \Whsuite\Inputs\Post as PostInput;

class CurrenciesController extends AdminController
{
    /**
     * scaffolding overrides
     *
     * see admin base controller for doc blocks
     */
    protected function indexToolbar()
    {
        return array(
            array(
                'url_route' => 'admin-currency',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'currency_management'
            ),
            array(
                'url_route' => 'admin-currency-add',
                'link_class' => '',
                'icon' => 'fa fa-plus',
                'label' => 'currency_add'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'code',
                'label' => 'currency_code',
            ),
            array(
                'field' => 'conversion_rate'
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
                'url_route' => 'admin-currency-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'icon' => 'fa fa-pencil',
                'label' => 'edit',
                'params' => array('id')
            ),
            'delete' => array(
                'url_route' => 'admin-currency-delete',
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
            'Currency.id' => array(
                'type' => 'hidden'
            ),
            'Currency.code' => array(
                'label' => 'currency_code'
            ),
            'Currency.prefix',
            'Currency.suffix',
            'Currency.decimals',
            'Currency.decimal_point',
            'Currency.thousand_separator',
            'Currency.conversion_rate',
            'Currency.auto_update'
        );

        return $fields;
    }

    protected function afterSave(&$main_model)
    {
        // Reset the currency gateway records
        GatewayCurrency::where('currency_id', '=', $main_model->id)->delete();

        // Load up all available gateways to ensure only active gateways are being selected.
        $all_gateways = Gateway::where('is_active', '=', '1')->get();

        $active_gateways = array();

        if (! empty($all_gateways)) {
            foreach($all_gateways as $gateway) {
                $active_gateways[] = $gateway->id;
            }
        }

        $gateway_sort = PostInput::get('data.Currency.Gateway');
        $gateway_array = explode(",", $gateway_sort);

        $gateways = array();

        $date = \Carbon\Carbon::now();

        foreach ($gateway_array as $sort => $gateway_id) {
            if (in_array($gateway_id, $active_gateways)) {
                $gateways[] = array(
                    'gateway_id' => $gateway_id,
                    'currency_id' => $main_model->id,
                    'sort' => $sort,
                    'created_at' => $date,
                    'updated_at' => $date
                );
            }
        }

        if (! empty($gateways)) {
            GatewayCurrency::insert($gateways);
        }
    }

    public function getExtraData($model)
    {
        $gateway_currencies = GatewayCurrency::where('currency_id', '=', $model->id)
            ->orderBy('sort', 'asc')
            ->get();

        $selected_gateways = array();
        $selected_gateway_ids = array();

        if (! empty($gateway_currencies)) {
            foreach ($gateway_currencies as $gateway_currency) {

                $gateway = $gateway_currency->Gateway()->first();
                $selected_gateways[] = $gateway;
                $selected_gateway_ids[] = $gateway->id;
            }
        }

        $this->view->set('selected_gateways', $selected_gateways);

        $available_gateways = array();

        if (! empty($selected_gateway_ids)) {
            $gateways = Gateway::whereNotIn('id', $selected_gateway_ids)->where('is_active', '=', '1')->get();
        } else {
            $gateways = Gateway::where('is_active', '=', '1')->get();
        }

        if (! empty($gateways)) {
            foreach($gateways as $gateway) {
                $available_gateways[$gateway->id] = $gateway->name;
            }
        }

        $this->view->set('available_gateways', $available_gateways);

    }

    public function form($id = null)
    {
        $this->render_view = 'currencies/form.php';
        $this->assets->addScript('currencies.js');

        return parent::form($id);
    }

}
