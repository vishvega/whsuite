<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <div class="row">
                <div class="col-lg-4 text-center"><?php echo $lang->get('welcome_back'); ?>, <b><?php echo $client->first_name.' '.$client->last_name; ?></b>!</div>
                <div class="col-lg-4 text-center"><b><?php echo $lang->get('account_credit'); ?>:</b> <?php echo $client_credit; ?></div>
                <div class="col-lg-4 text-center"><b><?php echo $lang->get('active_services'); ?>:</b> <a href="#"><?php echo $active_service_count; ?></a></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default" id="account-details">
            <div class="panel-heading"><?php echo $lang->get('account_details'); ?></div>
            <div class="panel-body">
                <b><?php echo $client->first_name.' '.$client->last_name; ?></b><br>
                <?php if($client->company !=''): ?>
                    <b><?php echo $client->company; ?></b><br>
                <?php endif; ?>
                <?php echo $client->address1; ?><br>
                <?php if($client->address2 !=''): ?>
                    <?php echo $client->address2; ?><br>
                <?php endif; ?>
                <?php echo $client->city; ?><br>
                <?php echo $client->state; ?><br>
                <?php echo $client->postcode; ?><br>
                <?php echo $client->country; ?><br>
                <br>
                <b><?php echo $lang->get('telephone'); ?>:</b> <?php echo $client->phone; ?><br>
                <b><?php echo $lang->get('email'); ?>:</b> <?php echo $client->email; ?><br>
                <br>
                <div class="row">
                    <div class="col-lg-6 text-center">
                        <a href="<?php echo $router->generate('client-profile'); ?>" class="btn btn-default"><?php echo $lang->get('edit_details'); ?></a>
                    </div>
                    <div class="col-lg-6 text-center">
                        <a href="<?php echo $router->generate('client-billing'); ?>" class="btn btn-default"><?php echo $lang->get('manage_billing_details'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default panel-height" id="announcements">
            <div class="panel-heading"><?php echo $lang->get('announcements'); ?> <a href="<?php echo $router->generate('client-announcements'); ?>" class="pull-right btn btn-default btn-xs"><?php echo $lang->get('view_all'); ?></a></div>
            <div class="panel-body">
                <div class="announcement-container">
                    <?php if($announcements->count() > 0): ?>
                        <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement">
                            <h4>
                                <?php
                                    $Carbon = \Carbon\Carbon::parse(
                                        $announcement->publish_date,
                                        $date['timezone']
                                    );
                                    echo $Carbon->format($date['short_date']);
                                ?> - <a href="<?php echo $router->generate('client-announcement', array('id' => $announcement->id)); ?>"><?php echo $announcement->title; ?></a>
                            </h4>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="alert alert-info text-center"><?php echo $lang->get('no_announcements_found'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default panel-tabs panel-newsbox">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#services" data-toggle="tab"><?php echo $lang->get('services'); ?></a></li>
                    <li><a href="#invoices" data-toggle="tab"><?php echo $lang->get('invoices'); ?></a></li>
                    <?php App::get('hooks')->callListeners('client-dashboard-tab-nav'); ?>
                </ul>
            </div>
            <div class="tab-content">
                <div class="panel-content panel-table tab-pane active" id="services">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $lang->get('service'); ?></th>
                                <th class="text-center"><?php echo $lang->get('status'); ?></th>
                                <th class="text-center"><?php echo $lang->get('next_renewal'); ?></th>
                                <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($services->count() > 0): ?>

                                <?php
                                foreach ($services as $service):
                                    $product = $service->Product()->first();
                                    $product_type = $product->ProductType()->first();

                                    $domain = null;

                                    if ($product_type->is_domain == '1'):
                                        $domain_details = $service->Domain()->first();
                                        $domain = $domain_details->domain;
                                    elseif ($product_type->is_hosting == '1'):
                                        $hosting_details = $service->Hosting()->first();
                                        $domain = $hosting_details->domain;
                                    endif;
                                ?>
                                    <tr>
                                        <td><a href="<?php echo $router->generate('client-manage-service', array('service_id' => $service->id)); ?>"><?php echo $product->name; ?> (<?php echo $product_type->name; ?>) <?php echo $domain; ?></a></td>
                                        <td class="text-center">
                                            <?php if ($service->status == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                            <?php elseif ($service->status == '2'): ?>
                                                <span class="label label-warning"><?php echo $lang->get('suspended'); ?></span>
                                            <?php elseif ($service->status == '3'): ?>
                                                <span class="label label-danger"><?php echo $lang->get('terminated'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-default"><?php echo $lang->get('pending'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $service->next_renewal,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td class="text-right"><a href="<?php echo $router->generate('client-manage-service', array('service_id' => $service->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('manage'); ?></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="panel-content panel-table tab-pane" id="invoices">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $lang->get('invoice_no'); ?></th>
                                <th class="text-center"><?php echo $lang->get('date_created'); ?></th>
                                <th class="text-center"><?php echo $lang->get('date_due'); ?></th>
                                <th class="text-center"><?php echo $lang->get('total'); ?></th>
                                <th class="text-center"><?php echo $lang->get('status'); ?></th>
                                <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($invoices->count() > 0): ?>

                                <?php
                                foreach ($invoices as $invoice):
                                    $currency = $invoice->Currency()->first();
                                ?>
                                    <tr>
                                        <td><a href="<?php echo $router->generate('client-manage-invoice', array('id' => $invoice->id)); ?>">Invoice #<?php echo $invoice->invoice_no; ?></a></td>
                                        <td class="text-center">
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $invoice->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $invoice->date_due,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td class="text-center"><?php echo App::get('money')->format($invoice->total, $currency->code); ?></td>
                                        <td class="text-center">
                                            <?php if ($invoice->status == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('paid'); ?></span>
                                            <?php elseif ($invoice->status == '2'): ?>
                                                <span class="label label-default"><?php echo $lang->get('void'); ?></span>
                                            <?php else: ?>
                                                <span class="label label-danger"><?php echo $lang->get('unpaid'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right"><a href="<?php echo $router->generate('client-manage-invoice', array('id' => $invoice->id)); ?>" class="btn btn-primary btn-xs"><?php echo $lang->get('manage'); ?></a></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php App::get('hooks')->callListeners('client-dashboard-tab-content'); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
