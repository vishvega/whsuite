<?php echo $view->fetch('elements/header.php'); ?>

    <div class="panel panel-primary">
        <div class="panel-heading"><?php echo $title; ?></div>
        <div class="panel-content">
            <?php
            echo $forms->open(
            array(
                'action' => $router->generate('client-service-domain-contacts', array('service_id' => $purchase->id)),
                'method' => 'post'
            )); ?>

                <?php echo $forms->select('registrant_contact', $lang->get('registrant_contact'), array('options' => $registrant_contacts)); ?>
                <?php echo $forms->select('administrative_contact', $lang->get('administrative_contact'), array('options' => $administrative_contacts)); ?>
                <?php echo $forms->select('technical_contact', $lang->get('technical_contact'), array('options' => $technical_contacts)); ?>
                <?php echo $forms->select('billing_contact', $lang->get('billing_contact'), array('options' => $billing_contacts)); ?>

                <div class="form-actions">
                    <?php echo $forms->submit('submit', $lang->get('update_contact_details')) ;?>
                </div>

            <?php echo $forms->close(); ?>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
