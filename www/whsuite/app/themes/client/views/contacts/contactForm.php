<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <?php echo $forms->open(array('action' => '', 'method' => 'post')); ?>

                    <?php echo $forms->select('Contact.title', $lang->get('title'), array('options' => $contact_titles)); ?>
                    <?php echo $forms->input('Contact.first_name', $lang->get('first_name')); ?>
                    <?php echo $forms->input('Contact.last_name', $lang->get('last_name')); ?>
                    <?php echo $forms->input('Contact.company', $lang->get('company')); ?>
                    <?php echo $forms->input('Contact.job_title', $lang->get('job_title')); ?>

                    <?php if(isset($contact_types)): ?>
                        <?php echo $forms->select('Contact.contact_type', $lang->get('contact_type'), array('options' => $contact_types)); ?>
                    <?php else: ?>
                        <?php echo $forms->input('Contact.contact_type', $lang->get('contact_type'), array('disabled' => 'disabled', 'value' => $lang->get($contact->contact_type))); ?>
                    <?php endif; ?>
                    <fieldset>
                        <legend><?php echo $lang->get('contact_details'); ?></legend>
                        <?php echo $forms->input('Contact.email', $lang->get('email')); ?>
                        <?php echo $forms->input('Contact.address1', $lang->get('address1')); ?>
                        <?php echo $forms->input('Contact.address2', $lang->get('address2')); ?>
                        <?php echo $forms->input('Contact.address3', $lang->get('address3')); ?>
                        <?php echo $forms->input('Contact.city', $lang->get('city')); ?>
                        <?php echo $forms->input('Contact.state', $lang->get('state')); ?>
                        <?php echo $forms->input('Contact.postcode', $lang->get('postcode')); ?>
                        <?php echo $forms->select('Contact.country', $lang->get('country'), array('options' => $country_list)); ?>

                        <?php echo $forms->input('Contact.phone_cc', $lang->get('telephone_cc')); ?>
                        <?php echo $forms->input('Contact.phone', $lang->get('telephone')); ?>

                        <?php echo $forms->input('Contact.fax_cc', $lang->get('fax_cc')); ?>
                        <?php echo $forms->input('Contact.fax', $lang->get('fax')); ?>
                    </fieldset>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('save')); ?>
                    </div>
                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
