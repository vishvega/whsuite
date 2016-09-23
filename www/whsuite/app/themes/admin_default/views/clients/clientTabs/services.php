<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('services'); ?></div>
    <div class="panel-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('service'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('type'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('date_created'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('date_renewal'); ?></th>
                    <th><?php echo $lang->get('status'); ?></th>
                    <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if($products->count() > 0):
                foreach ($products as $product):
                    $productInfo = $product->Product()->first();
                    $productType = $productInfo->ProductType()->first();

                    if ($productType->is_domain):
                        $serviceData = $product->Domain()->first();
                    else:
                        $serviceData = $product->Hosting()->first();
                    endif;
            ?>
                <tr>
                    <td><?php echo $productInfo->name; ?> (<?php echo $serviceData->domain; ?>)</td>
                    <td><?php echo $productType->name; ?></td>
                    <td>
                        <?php
                            $Carbon = \Carbon\Carbon::parse(
                                $product->created_at,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        ?>
                    </td>
                    <td>
                        <?php
                            $Carbon = \Carbon\Carbon::parse(
                                $product->next_renewal,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        ?>
                    </td>
                    <td>
                        <?php if ($product->status == '1'): ?>
                            <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                        <?php elseif ($product->status == '2'): ?>
                            <span class="label label-warning"><?php echo $lang->get('suspended'); ?></span>
                        <?php elseif ($product->status == '3'): ?>
                            <span class="label label-important"><?php echo $lang->get('terminated'); ?></span>
                        <?php else: ?>
                            <span class="label label-warning"><?php echo $lang->get('pending'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-right">
                        <a href="<?php echo $router->generate('admin-client-service', array('id' => $client->id, 'service_id' => $product->id)); ?>" class="btn btn-primary btn-small">
                            <?php echo $lang->get('manage'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-center">
                        <div class="row">
                            <div class="col-sm-12">
                                <a href="<?php echo $router->generate('admin-client-services-paging', array('id' => $client->id, 'page' => '1')); ?>" class="btn btn-small btn-primary">
                                    <i class="fa fa-list"></i> <?php echo $lang->get('all_services'); ?>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
