<?php
/**
 * Order Controller
 *
 * The orders controller handles all order related actions such as manually
 * creating orders, and viewing order details.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class OrderController extends AdminController
{
    protected function indexColumns()
    {
        $lang = \App::get('translation');

        return array(
            array(
                'field' => 'order_no'
            ),
            array(
                'field' => array(
                    'Client.first_name',
                    'Client.last_name'
                ),
                'label' => 'name'
            ),
            array(
                'type' => 'options',
                'field' => 'status',
                'label' => 'status',
                'option_labels' => array(
                    '0' => '<span class="label label-warning">' . $lang->get('pending') . '</span>',
                    '1' => '<span class="label label-success">' . $lang->get('active') . '</span>',
                    '2' => '<span class="label label-danger">' . $lang->get('terminated') . '</span>'
                )
            ),
            array(
                'field' => 'created_at'
            ),
            array(
                'action' => 'view',
                'label' => null
            )
        );
    }

    protected function indexActions()
    {
        return array(
            'view' => array(
                'url_route' => 'admin-order-view',
                'link_class' => 'btn btn-primary btn-small',
                'icon' => false,
                'label' => 'view',
                'params' => array('id')
            )
        );
    }

    public function viewOrder($id)
    {
        $order = Order::find($id);

        if (empty($order)) {
            $this->redirect('admin-order');
        }

        App::factory('\App\Libraries\OrderHelper');

        $title = $this->lang->get('view_order').' #'.$order->order_no;
        $this->view->set('title', $title);

        App::get('breadcrumbs')->add($this->lang->get('dashboard'), 'admin-home');
        App::get('breadcrumbs')->add($this->lang->get('order_management'), 'admin-order');
        App::get('breadcrumbs')->add($title);
        App::get('breadcrumbs')->build();

        $client = $order->Client()->first();
        $purchases = $order->ProductPurchase()->get();
        $invoice = $order->Invoice()->first();

        $this->view->set('order', $order);
        $this->view->set('client', $client);
        $this->view->set('purchases', $purchases);
        $this->view->set('invoice', $invoice);

        $order_statuses = array(
            '0' => App::get('translation')->get('pending'),
            '1' => App::get('translation')->get('active'),
            '2' => App::get('translation')->get('terminated'),
        );
        $this->view->set('order_statuses', $order_statuses);

        $this->view->display('order/viewOrder.php');
    }

    public function activateOrder($id)
    {
        $order = Order::find($id);

        if (empty($order)) {
            $this->redirect('admin-order');
        }
        $order_helper = App::factory('\App\Libraries\OrderHelper');

        $order_helper->activateOrder($id);

        App::get('session')->setFlash('success', $this->lang->get('order_activated'));
        $this->redirect('admin-order-view', ['id' => $order->id]);
    }

    public function pendingOrder($id)
    {
        $order = Order::find($id);

        if (empty($order)) {
            $this->redirect('admin-order');
        }
        $order->status = 0;
        if ($order->save()) {
            App::get('session')->setFlash('success', $this->lang->get('order_status_updated'));
        } else {
            App::get('session')->setFlash('success', $this->lang->get('error_updating_order_status'));
        }

        $this->redirect('admin-order-view', ['id' => $order->id]);
    }

    public function terminateOrder($id)
    {
        $order = Order::find($id);

        if (empty($order)) {
            $this->redirect('admin-order');
        }

        $order_helper = App::factory('\App\Libraries\OrderHelper');
        $order_helper->terminateOrder($id);

        App::get('session')->setFlash('success', $this->lang->get('order_status_updated'));
        $this->redirect('admin-order-view', ['id' => $order->id]);
    }
}
