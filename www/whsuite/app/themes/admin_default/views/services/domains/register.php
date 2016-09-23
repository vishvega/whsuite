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
                                'action' => $router->generate('admin-service-domain-register', array('id' => $client->id, 'service_id' => $purchase->id)),
                                'method' => 'post',
                                'id' => 'registerForm'
                            )); ?>
                                <?php echo $forms->select('years', $lang->get('registration_period'), array('options' => $years)); ?>
                                <div id="nameserversContainer">
                                    <?php if(isset($post_data_nameservers) && is_array($post_data_nameservers)): ?>
                                        <?php foreach($post_data_nameservers as $nameserver): ?>
                                            <?php echo $forms->input('nameservers[]', $lang->get('nameservers'), array('value' => $nameserver)); ?>
                                        <?php endforeach; ?>
                                        <div id="nameservers"><?php echo $forms->input('nameservers[]', $lang->get('nameservers')); ?></div>
                                    <?php else: ?>
                                        <div id="nameservers"><?php echo $forms->input('nameservers[]', $lang->get('nameservers')); ?></div>
                                        <?php echo $forms->input('nameservers[]', $lang->get('nameservers')); ?>
                                    <?php endif; ?>
                                </div>
                                <p class="help-block"><a href="#" id="moreNameservers"><?php echo $lang->get('add_more'); ?></a></p>

                                <?php if(isset($registration_fields) && $registration_fields !=''): ?>
                                    <fieldset>
                                        <legend><?php echo $lang->get('additional_details'); ?></legend>
                                        <?php echo $registration_fields; ?>
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
                                    <?php echo $forms->submit('submit', $lang->get('register_domain')) ;?>
                                </div>

                            <?php echo $forms->close(); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    $( "#manage" ).load( "<?php echo $manage_route; ?>", function() {

    });
    </script>

<?php echo $view->fetch('elements/footer.php');
