<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <?php echo $forms->open(array('action' => $router->generate('client-manage-ach', array('id' => $ach->id)), 'method' => 'post')); ?>
                    <?php echo $forms->input('Ach.first_name', $lang->get('first_name')); ?>
                    <?php echo $forms->input('Ach.last_name', $lang->get('last_name')); ?>
                    <?php echo $forms->input('Ach.company', $lang->get('company')); ?>
                    <?php echo $forms->input('Ach.email', $lang->get('email')); ?>

                    <?php echo $forms->input('Ach.address1', $lang->get('address1')); ?>
                    <?php echo $forms->input('Ach.address2', $lang->get('address2')); ?>
                    <?php echo $forms->input('Ach.city', $lang->get('city')); ?>
                    <?php echo $forms->input('Ach.state', $lang->get('state')); ?>
                    <?php echo $forms->input('Ach.postcode', $lang->get('postcode')); ?>
                    <?php echo $forms->select('Ach.country', $lang->get('country'), array('options' => $country_list)); ?>

                    <?php echo $forms->checkbox('Ach.is_active', $lang->get('active')); ?>
                    <span class="help-block"><?php echo $lang->get('ach_can_be_disabled'); ?></span>

                    <?php echo $forms->checkbox('Ach.is_default', $lang->get('default_ach')); ?>
                    <span class="help-block"><?php echo $lang->get('ach_can_be_default'); ?></span>
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
                    <?php
                        echo $forms->input(
                            'Ach.account_number',
                            $lang->get('reenter_account_number'),
                            array(
                                'value' => $ach->account_number
                            )
                        );
                    ?>
                    <?php
                        echo $forms->input(
                            'Ach.account_routing_number',
                            $lang->get('reenter_routing_number'),
                            array(
                                'value' => $ach->account_routing_number
                            )
                        );
                    ?>
                    <?php echo $forms->select('Ach.account_type', $lang->get('account_type'), array('options' => $ach_account_types, 'value' => '')); ?>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('update')); ?>
                        <a href="<?php echo $router->generate('client-delete-ach', array('id' => $ach->id)); ?>" class="btn btn-danger"><?php echo $lang->get('delete'); ?></a>
                    </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
