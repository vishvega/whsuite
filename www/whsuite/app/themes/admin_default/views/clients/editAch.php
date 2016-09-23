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
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-clientach-edit', array('id' => $client->id, 'ach_id' => $ach->id)))); ?>

                                <?php echo $forms->input('Ach.first_name', $lang->get('first_name')); ?>
                                <?php echo $forms->input('Ach.last_name', $lang->get('last_name')); ?>
                                <?php echo $forms->input('Ach.company', $lang->get('company')); ?>
                                <?php echo $forms->input('Ach.email', $lang->get('email')); ?>
                                <?php echo $forms->input('Ach.address1', $lang->get('address1')); ?>
                                <?php echo $forms->input('Ach.address2', $lang->get('address2')); ?>
                                <?php echo $forms->input('Ach.city', $lang->get('city')); ?>
                                <?php echo $forms->input('Ach.state', $lang->get('state')); ?>
                                <?php echo $forms->input('Ach.postcode', $lang->get('postcode')); ?>
                                <?php echo $forms->input('Ach.country', $lang->get('country')); ?>
                                <?php echo $forms->checkbox('Ach.is_active', $lang->get('active')); ?>
                                <?php echo $forms->checkbox('Ach.is_default', $lang->get('default_method_for_payments')); ?>
                                <div class="clearfix"></div>
                                <hr>
                                <?php
                                    echo $forms->input(
                                        'Ach.currency',
                                        $lang->get('currency'),
                                        array(
                                            'disabled' => 'disabled',
                                            'value' => $ach->Currency->code
                                        )
                                    );
                                ?>
                                <?php echo $forms->select('Ach.account_type', $lang->get('account_type'), array('options' => $account_types)); ?>
                                <?php echo $forms->input('Ach.account_number', $lang->get('reenter_account_number')); ?>
                                <span class="help-block">
                                    <a href="#accountSecurityModal" class="showSecurityModal">
                                        <?php echo $lang->get('decrypt_account_number'); ?>
                                    </a>
                                </span>
                                <?php echo $forms->input('Ach.account_routing_number', $lang->get('reenter_routing_number')); ?>
                                <span class="help-block">
                                    <a href="#routingSecurityModal" class="showSecurityModal">
                                        <?php echo $lang->get('decrypt_routing_number'); ?>
                                    </a>
                                </span>
                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                    <a href="<?php echo $router->generate('admin-clientach-delete', array('id' => $client->id, 'ach_id' => $ach->id)); ?>" class="pull-right btn btn-danger" onclick="javascrip:confirm('<?php echo $lang->get('confirm_delete'); ?>');"><?php echo $lang->get('delete'); ?></a>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    echo $view->fetch(
        'security/modalDecrypt.php',
        array(
            'route_override' => App::get('router')->generate(
                'admin-clientach-decrypt',
                array(
                    'id' => $client->id,
                    'ach_id' => $ach->id
                )
            ),
            'modal_id' => 'accountSecurityModal'
        )
    );
    echo $view->fetch(
        'security/modalDecrypt.php',
        array(
            'route_override' => App::get('router')->generate(
                'admin-clientach-decrypt-routing',
                array(
                    'id' => $client->id,
                    'ach_id' => $ach->id
                )
            ),
            'modal_id' => 'routingSecurityModal'
        )
    );

echo $view->fetch('elements/footer.php');
