<?php

use \Illuminate\Support\Str;

class WidgetsController extends \AdminController
{

    /**
     * recent orders widget for the dashboard.
     * Shows half of what is shown on the first orders page
     *
     */
    public function recentOrders()
    {
        $per_page = ceil((\App::get('configs')->get('settings.general.results_per_page') / 2) );

        $orders = Order::paginate(
            $per_page,
            1,
            array(),
            'status',
            'asc'
        );

        $order_helper = App::factory('\App\Libraries\OrderHelper');
        $this->view->set('orders', $orders);

        $this->view->display('widgets/recentOrders.php');
    }



}
