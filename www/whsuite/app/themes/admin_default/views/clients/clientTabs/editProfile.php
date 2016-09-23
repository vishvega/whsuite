<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('profile'); ?></div>
    <div class="panel-content">
        <?php echo $forms->open(array('action' => $router->generate('admin-client-edit', array('id' => $client->id)), 'role' => 'form')); ?>
            <?php echo $forms->input('Client.first_name', $lang->get('first_name'), array('value' => $client->first_name)); ?>
            <?php echo $forms->input('Client.last_name', $lang->get('last_name'), array('value' => $client->last_name)); ?>
            <?php echo $forms->input('Client.company', $lang->get('company'), array('value' => $client->company)); ?>

            <fieldset>
                <legend><?php echo $lang->get('contact_details'); ?></legend>
                <?php echo $forms->input('Client.email', $lang->get('email'), array('value' => $client->email)); ?>
                <?php echo $forms->input('Client.address1', $lang->get('address1'), array('value' => $client->address1)); ?>
                <?php echo $forms->input('Client.address2', $lang->get('address2'), array('value' => $client->address2)); ?>
                <?php echo $forms->input('Client.city', $lang->get('city'), array('value' => $client->city)); ?>
                <?php echo $forms->input('Client.state', $lang->get('state'), array('value' => $client->state)); ?>
                <?php echo $forms->input('Client.postcode', $lang->get('postcode'), array('value' => $client->ppostcode)); ?>
                <?php echo $forms->select('Client.country', $lang->get('country'), array('options' => $country_list, 'value' => $client->country)); ?>
                <?php echo $forms->input('Client.phone', $lang->get('telephone'), array('value' => $client->phone)); ?>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->get('change_password'); ?></legend>
                <?php echo $forms->password('Client.password', $lang->get('password')); ?>
                <?php echo $forms->password('Client.confirm_password', $lang->get('confirm_password')); ?>
            </fieldset>

            <fieldset>
                <legend><?php echo $lang->get('account_options'); ?></legend>
                <?php echo $forms->select('Client.status', $lang->get('status'), array('options' => Client::formattedStatusList(), 'value' => $client->status)); ?>
                <?php echo $forms->select('Client.language_id', $lang->get('language'), array('options' => Language::formattedList(), 'value' => $client->language_id)); ?>
                <?php echo $forms->checkbox('Client.is_taxexempt', $lang->get('is_taxexempt')); ?>
                <?php echo $forms->checkbox('Client.html_emails', $lang->get('html_emails')); ?>
            </fieldset>

            <?php echo $client->customFields(false); ?>

            <div class="form-actions">
                <?php echo $forms->submit('submit', $lang->get('save'), array('class' => 'btn btn-primary')); ?>
                <span class="required"><?php echo $lang->get('required_field'); ?></span>
            </div>
        <?php echo $forms->close(); ?>
    </div>
</div>