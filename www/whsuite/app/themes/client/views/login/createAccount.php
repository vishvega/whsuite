<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $lang->get('create_account'); ?></div>
            <div class="panel-body">
                <?php echo $forms->open(array('action' => $router->generate('client-create-account'), 'method' => 'post')); ?>

                    <?php echo $forms->input('Client.first_name', $lang->get('first_name')); ?>
                    <?php echo $forms->input('Client.last_name', $lang->get('last_name')); ?>
                    <?php echo $forms->input('Client.company', $lang->get('company')); ?>

                    <fieldset>
                        <legend><?php echo $lang->get('contact_details'); ?></legend>
                        <?php echo $forms->input('Client.email', $lang->get('email')); ?>
                        <?php echo $forms->input('Client.address1', $lang->get('address1')); ?>
                        <?php echo $forms->input('Client.address2', $lang->get('address2')); ?>
                        <?php echo $forms->input('Client.city', $lang->get('city')); ?>
                        <?php echo $forms->input('Client.state', $lang->get('state')); ?>
                        <?php echo $forms->input('Client.postcode', $lang->get('postcode')); ?>
                        <?php echo $forms->select('Client.country', $lang->get('country'), array('options' => $country_list)); ?>
                        <?php echo $forms->input('Client.phone', $lang->get('telephone')); ?>
                    </fieldset>

                    <fieldset>
                        <legend><?php echo $lang->get('password'); ?></legend>
                        <?php echo $forms->password('Client.password', $lang->get('password')); ?>
                        <?php echo $forms->password('Client.confirm_password', $lang->get('confirm_password')); ?>
                    </fieldset>

                    <fieldset>
                        <legend><?php echo $lang->get('account_options'); ?></legend>
                        <?php echo $forms->select('Client.language_id', $lang->get('language'), array('options' => Language::formattedList('id', 'name', array(), 'name', 'asc'), 'value' => $default_language_id)); ?>
                        <?php echo $forms->select('Client.currency_id', $lang->get('default_currency'), array('options' => Currency::formattedList('id', 'code'))); ?>
                        <?php echo $forms->checkbox('Client.html_emails', $lang->get('html_emails')); ?>
                    </fieldset>

                    <?php echo $client->customFields(); ?>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('create_account')); ?>
                    </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
