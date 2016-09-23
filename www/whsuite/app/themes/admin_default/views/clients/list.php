<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('statistics'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td><b><?php echo $lang->get('active_clients'); ?>:</b></td>
                                        <td><?php echo $active_clients; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('inactive_clients'); ?>:</b></td>
                                        <td><?php echo $inactive_clients; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('fraud_clients'); ?>:</b></td>
                                        <td><?php echo $fraud_clients; ?></td>
                                    </tr>
                                    <tr>
                                        <td><b><?php echo $lang->get('total_clients'); ?>:</b></td>
                                        <td><?php echo $total_clients; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="panel-group" id="accordion">
                        <div class="panel panel-primary">
                            <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                <?php echo $lang->get('filter_results'); ?>
                                <div class="collapse-icon"><i class="icon"></i></div>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse">
                                <div class="panel-content panel-form">
                                    <?php echo $forms->open(array('method' => 'get', 'role' => 'form', 'class' => 'form-vertical form-compact')); ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php echo $forms->select('status', $lang->get('status'), array('options' => $status_types)); ?>
                                                <?php echo $forms->input('first_name', $lang->get('first_name')); ?>
                                                <?php echo $forms->input('address1', $lang->get('address1')); ?>
                                                <?php echo $forms->input('post_code', $lang->get('postcode')); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php echo $forms->input('email_address', $lang->get('email')); ?>
                                                <?php echo $forms->input('last_name', $lang->get('last_name')); ?>
                                                <?php echo $forms->input('address2', $lang->get('address2')); ?>
                                                <?php echo $forms->select('country', $lang->get('country'), array('options' => $country_list)); ?>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <?php echo $forms->submit('submit', $lang->get('filter_results'), array('class' =>'btn btn-primary btn-block')); ?>
                                        </div>
                                    <?php $forms->close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $lang->get('client_management'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('name'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('email'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('status'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('last_login'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($clients as $client):
                                    ?>
                                    <tr>
                                        <td><?php echo $client['first_name'].' '.$client['last_name']; ?></td>
                                        <td><?php echo $client['email']; ?></td>
                                        <td>
                                            <?php if ($client['status'] == '1'): ?>
                                                <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                            <?php elseif ($client['status'] == '2'): ?>
                                                <span class="label label-danger"><?php echo $lang->get('fraud'); ?></span>
                                            <?php else: ?>
                                                <span class="label"><?php echo $lang->get('inactive'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $client['last_login']; ?></td>
                                        <td>
                                            <a href="<?php echo $router->generate('admin-client-profile', array('id' => $client['id'])); ?>" class="btn btn-primary btn-small pull-right">
                                                <i class="fa fa-user"></i> <?php echo $lang->get('manage'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><?php echo $pagination; ?></td>
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
