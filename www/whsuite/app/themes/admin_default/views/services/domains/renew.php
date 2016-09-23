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
                                'action' => $router->generate('admin-service-domain-renew', array('id' => $client->id, 'service_id' => $purchase->id)),
                                'method' => 'post',
                                'id' => 'renewForm'
                            )); ?>
                                <?php echo $forms->select('years', $lang->get('registration_period'), array('options' => $years)); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('renew_domain')) ;?>
                                </div>

                            <?php echo $forms->close(); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
