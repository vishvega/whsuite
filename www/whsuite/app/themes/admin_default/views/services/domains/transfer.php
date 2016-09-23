<?php echo $view->fetch('elements/header.php'); ?>


    <div class="content-inner">
        <div class="container">

            <div class="row">
                <div class="col-md-12">

                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php
                            echo $forms->open(
                            array(
                                'action' => $router->generate('admin-service-domain-transfer', array('id' => $client->id, 'service_id' => $purchase->id)),
                                'method' => 'post',
                                'id' => 'transferForm'
                            )); ?>

                                <?php echo $forms->input('auth_code', $lang->get('auth_code')); ?>

                                <?php if(isset($transfer_fields) && $transfer_fields !=''): ?>
                                    <fieldset>
                                        <legend><?php echo $lang->get('additional_details'); ?></legend>
                                        <?php echo $transfer_fields; ?>
                                    </fieldset>
                                <?php endif; ?>

                                <fieldset>
                                    <legend><?php echo $lang->get('domain_contacts'); ?></legend>

                                    <?php echo $forms->select('registrant_contact', $lang->get('registrant_contact'), array('options' => $registrant_contacts)); ?>
                                    <?php echo $forms->select('administrative_contact', $lang->get('administrative_contact'), array('options' => $administrative_contacts)); ?>
                                    <?php echo $forms->select('technical_contact', $lang->get('technical_contact'), array('options' => $technical_contacts)); ?>
                                    <?php echo $forms->select('billing_contact', $lang->get('billing_contact'), array('options' => $billing_contacts)); ?>

                                </fieldset>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('transfer_domain')) ;?>
                                </div>

                            <?php echo $forms->close(); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php');
