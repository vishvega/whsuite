<?php
use \Whsuite\Inputs\Post as PostInput;

class GatewaysController extends AdminController
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
                'url_route' => 'admin-gateway',
                'link_class' => '',
                'icon' => 'fa fa-list-ul',
                'label' => 'gateway_management'
            )
        );
    }

    protected function indexColumns()
    {
        return array(
            array(
                'field' => 'name',
                'label' => 'name',
            ),
            array(
                'field' => 'is_active',
                'label' => 'active'
            ),
            array(
                'action' => 'manage',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'manage' => array(
                'url_route' => 'admin-gateway-edit',
                'link_class' => 'btn btn-primary btn-small pull-right',
                'label' => 'manage',
                'params' => array('id')
            )
        );
    }


    public function getExtraData($model)
    {
        $currencies = Currency::get();
        $excluded_currencies = array();

        $merchant_gateways = Gateway::where('is_merchant', '=', '1')->where('id', '!=', $model->id)->get();

        if ($model->is_merchant == '1') {
            foreach ($merchant_gateways as $merchant) {
                $merchant_currencies = $merchant->GatewayCurrency()->get();

                if (!empty($merchant_currencies)) {
                    foreach($merchant_currencies as $merchant_currency) {
                        $excluded_currencies[] = $merchant_currency->currency_id;
                    }
                }
            }
        }
    }

    public function form($id = null)
    {
        $this->render_view = 'gateways/form.php';

        return parent::form($id);
    }

    protected function formFields()
    {
        return array(
            'Gateway.id',
            'Gateway.name',
            'Gateway.is_active'
        );
    }


}
