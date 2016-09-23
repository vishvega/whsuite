<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <?php if(isset($client)): ?>
                <div class="col-lg-4">
                    <?php echo $view->fetch('clients/profileSidebar/info.php'); ?>
                </div>
                <div class="col-lg-8">
                <?php else: ?>
                <div class="col-lg-12">
                <?php endif; ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('service'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('client'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('type'); ?></th>
                                        <th data-hide="phone,tablet"><?php echo $lang->get('date_created'); ?></th>
                                        <th data-hide="phone,tablet"><?php echo $lang->get('date_renewal'); ?></th>
                                        <th><?php echo $lang->get('status'); ?></th>
                                        <th><?php echo $lang->get('manage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($services as $purchase):
                                        $productInfo = $purchase->Product()->first();
                                        $productType = $productInfo->ProductType()->first();

                                        if(!isset($client)):
                                            $client = $purchase->client->first();
                                        endif;

                                        if ($productType->is_domain):
                                            $serviceData = $purchase->Domain()->first();
                                        else:
                                            $serviceData = $purchase->Hosting()->first();
                                        endif;
                                    ?>
                                    <tr>
                                        <td><?php echo $productInfo->name; ?> (<?php echo $serviceData->domain; ?>)</td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-profile', array('id' => $client->id)); ?>">
                                                <?php echo $client->first_name.' '.$client->last_name; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $productType->name; ?></td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $purchase->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $purchase->next_renewal,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_date']);
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($purchase->status == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                            <?php elseif ($purchase->status == '2'): ?>
                                                <span class="label label-warning"><?php echo $lang->get('suspended'); ?></span>
                                            <?php elseif ($purchase->status == '3'): ?>
                                                <span class="label label-important"><?php echo $lang->get('terminated'); ?></span>
                                            <?php else: ?>
                                                <span class="label"><?php echo $lang->get('pending'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?php echo $router->generate('admin-client-service', array('id' => $client->id, 'service_id' => $purchase->id)); ?>">
                                                <?php echo $lang->get('manage'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-right"><?php echo $pagination; ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
