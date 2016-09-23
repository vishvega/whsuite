<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <?php echo $forms->open(array('action' => $router->generate('client-manage-cc', array('id' => $cc->id)), 'method' => 'post')); ?>
                    <?php echo $forms->input('Cc.first_name', $lang->get('first_name')); ?>
                    <?php echo $forms->input('Cc.last_name', $lang->get('last_name')); ?>
                    <?php echo $forms->input('Cc.company', $lang->get('company')); ?>
                    <?php echo $forms->input('Cc.email', $lang->get('email')); ?>

                    <?php echo $forms->input('Cc.address1', $lang->get('address1')); ?>
                    <?php echo $forms->input('Cc.address2', $lang->get('address2')); ?>
                    <?php echo $forms->input('Cc.city', $lang->get('city')); ?>
                    <?php echo $forms->input('Cc.state', $lang->get('state')); ?>
                    <?php echo $forms->input('Cc.postcode', $lang->get('postcode')); ?>
                    <?php echo $forms->select('Cc.country', $lang->get('country'), array('options' => $country_list)); ?>

                    <?php echo $forms->checkbox('Cc.is_active', $lang->get('active')); ?>
                    <span class="help-block"><?php echo $lang->get('card_can_be_disabled'); ?></span>

                    <?php echo $forms->checkbox('Cc.is_default', $lang->get('default_cc')); ?>
                    <span class="help-block"><?php echo $lang->get('card_can_be_default'); ?></span>
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
                    <?php
                        echo $forms->input(
                            'Cc.account_number',
                            $lang->get('reenter_card_number'),
                            array(
                                'value' => $cc->account_number
                            )
                        );
                    ?>
                    <?php
                        echo $forms->input(
                            'Cc.account_expiry',
                            $lang->get('reenter_expiry_date'),
                            array(
                                'placeholder' => 'MMYY',
                                'value' => $cc->account_expiry
                            )
                        );
                    ?>
                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('update')); ?>
                        <a href="<?php echo $router->generate('client-delete-cc', array('id' => $cc->id)); ?>" class="btn btn-danger"><?php echo $lang->get('delete'); ?></a>
                    </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
