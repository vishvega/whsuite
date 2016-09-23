<?php echo $view->fetch('elements/header.php'); ?>

<div class="content-full">
    <div class="row">
        <div class="col-lg-3 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-heading"><?php echo $lang->get('search_results'); ?></div>
                <div class="panel-content">
                    <ul class="nav nav-pills nav-stacked">
                        <li class="active">
                            <a href="#client-results" role="tab" data-toggle="tab">
                                <?php echo $lang->get('clients'); ?>
                            </a>
                        </li>

                        <li>
                            <a href="#domain-results" role="tab" data-toggle="tab">
                                <?php echo $lang->get('domains'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9 col-md-9">
            <div class="tab-content">
                <div class="tab-pane active" id="client-results">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('clients'); ?></div>
                        <div class="panel-content">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('name'); ?></th>
                                        <th><?php echo $lang->get('email'); ?></th>
                                        <th><?php echo $lang->get('company'); ?></th>
                                        <th><?php echo $lang->get('manage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($client_results as $client): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client', array('id' => $client->id)); ?>">
                                                <?php echo $client->first_name; ?> <?php echo $client->last_name; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $client->email; ?></td>
                                        <td><?php echo $client->company; ?></td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client', array('id' => $client->id)); ?>" class="btn btn-sm btn-primary">
                                                <?php echo $lang->get('manage'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if(count($client_results) < 1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <?php echo $lang->get('no_results_found'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="domain-results">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('domains'); ?></div>
                        <div class="panel-content">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('domain'); ?></th>
                                        <th><?php echo $lang->get('client'); ?></th>
                                        <th><?php echo $lang->get('manage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($domain_results as $domain): ?>
                                    <?php $purchase = $domain->ProductPurchase()->first(); ?>
                                    <?php $client = $purchase->Client()->first(); ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-service', array('id' => $client->id, 'service_id' => $purchase->id)); ?>">
                                                <?php echo $domain->domain; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client', array('id' => $client->id)); ?>">
                                                <?php echo $client->first_name; ?> <?php echo $client->last_name; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-service', array('id' => $client->id, 'service_id' => $purchase->id)); ?>" class="btn btn-sm btn-primary">
                                                <?php echo $lang->get('manage'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if(count($domain_results) < 1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <?php echo $lang->get('no_results_found'); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php echo $view->fetch('elements/footer.php'); ?>