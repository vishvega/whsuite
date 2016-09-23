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
                            <?php echo $forms->open(array('action' => $router->generate('admin-clientcc-edit', array('id' => $client->id, 'cc_id' => $cc->id)))); ?>

                                <?php echo $forms->input('Cc.first_name', $lang->get('first_name')); ?>
                                <?php echo $forms->input('Cc.last_name', $lang->get('last_name')); ?>
                                <?php echo $forms->input('Cc.company', $lang->get('company')); ?>
                                <?php echo $forms->input('Cc.email', $lang->get('email')); ?>

                                <?php echo $forms->input('Cc.address1', $lang->get('address1')); ?>
                                <?php echo $forms->input('Cc.address2', $lang->get('address2')); ?>
                                <?php echo $forms->input('Cc.city', $lang->get('city')); ?>
                                <?php echo $forms->input('Cc.state', $lang->get('state')); ?>
                                <?php echo $forms->input('Cc.postcode', $lang->get('postcode')); ?>
                                <?php echo $forms->input('Cc.country', $lang->get('country')); ?>
                                <?php echo $forms->checkbox('Cc.is_active', $lang->get('active')); ?>
                                <?php echo $forms->checkbox('Cc.is_default', $lang->get('default_method_for_payments')); ?>
                                <div class="clearfix"></div>
                                <hr>
                                <?php
                                    echo $forms->input(
                                        'Cc.currency',
                                        $lang->get('currency'),
                                        array(
                                            'disabled' => 'disabled',
                                            'value' => $cc->Currency->code
                                        )
                                    );
                                ?>
                                <?php echo $forms->input('Cc.account_number', $lang->get('reenter_card_number')); ?>
                                <span class="help-block">
                                    <a href="#securityModal" class="showSecurityModal">
                                        <?php echo $lang->get('decrypt_card_number'); ?>
                                    </a>
                                </span>
                                <?php echo $forms->input('Cc.account_expiry', $lang->get('reenter_expiry_date'), array('placeholder' => 'MMYY')); ?>
                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                    <a href="<?php echo $router->generate('admin-clientcc-delete', array('id' => $client->id, 'cc_id' => $cc->id)); ?>" class="pull-right btn btn-danger" onclick="javascrip:confirm('<?php echo $lang->get('confirm_delete'); ?>');"><?php echo $lang->get('delete'); ?></a>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
echo $view->fetch('security/modalDecrypt.php');
echo $view->fetch('elements/footer.php');
