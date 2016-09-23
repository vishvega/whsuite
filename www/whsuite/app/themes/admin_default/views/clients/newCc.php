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
                            <?php echo $forms->open(array('action' => $router->generate('admin-clientcc-add', array('id' => $client->id)))); ?>

                                <?php echo $forms->input('Cc.first_name', $lang->get('first_name'), array('value' => $client->first_name)); ?>
                                <?php echo $forms->input('Cc.last_name', $lang->get('last_name'), array('value' => $client->last_name)); ?>
                                <?php echo $forms->input('Cc.company', $lang->get('company'), array('value' => $client->company)); ?>
                                <?php echo $forms->input('Cc.email', $lang->get('email'), array('value' => $client->email)); ?>

                                <?php echo $forms->input('Cc.address1', $lang->get('address1'), array('value' => $client->address1)); ?>
                                <?php echo $forms->input('Cc.address2', $lang->get('address2'), array('value' => $client->address2)); ?>
                                <?php echo $forms->input('Cc.city', $lang->get('city'), array('value' => $client->city)); ?>
                                <?php echo $forms->input('Cc.state', $lang->get('state'), array('value' => $client->state)); ?>
                                <?php echo $forms->input('Cc.postcode', $lang->get('postcode'), array('value' => $client->postcode)); ?>
                                <?php echo $forms->input('Cc.country', $lang->get('country'), array('value' => $client->country)); ?>
                                <?php echo $forms->checkbox('Cc.is_active', $lang->get('active')); ?>
                                <?php echo $forms->checkbox('Cc.is_default', $lang->get('default_method_for_payments')); ?>
                                <div class="clearfix"></div>
                                <hr>
                                <?php echo $forms->select('Cc.currency_id', $lang->get('currency'), array('options' => $currencies)); ?>
                                <?php echo $forms->input('Cc.account_number', $lang->get('card_number')); ?>
                                <?php echo $forms->input('Cc.account_expiry', $lang->get('expiry_date'), array('placeholder' => 'MMYY')); ?>
                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
echo $view->fetch('elements/footer.php');
