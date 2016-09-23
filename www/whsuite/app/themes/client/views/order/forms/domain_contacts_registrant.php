<div class="well" id="containerRegistrantContacts">
    <h4><?php echo $lang->get('registrant_contact_details'); ?></h4>
    <?php echo $forms->select('Registrant.title', $lang->get('title'), array('options' => $contact_titles)); ?>

    <?php echo $forms->input('Registrant.first_name', $lang->get('first_name')); ?>
    <?php echo $forms->input('Registrant.last_name', $lang->get('last_name')); ?>
    <?php echo $forms->input('Registrant.company', $lang->get('company')); ?>
    <?php echo $forms->input('Registrant.job_title', $lang->get('job_title')); ?>

    <?php echo $forms->input('Registrant.email', $lang->get('email')); ?>
    <?php echo $forms->input('Registrant.address1', $lang->get('address1')); ?>
    <?php echo $forms->input('Registrant.address2', $lang->get('address2')); ?>
    <?php echo $forms->input('Registrant.address3', $lang->get('address3')); ?>
    <?php echo $forms->input('Registrant.city', $lang->get('city')); ?>
    <?php echo $forms->input('Registrant.state', $lang->get('state')); ?>
    <?php echo $forms->input('Registrant.postcode', $lang->get('postcode')); ?>
    <?php echo $forms->select('Registrant.country', $lang->get('country'), array('options' => $country_list)); ?>

    <?php echo $forms->input('Registrant.phone_cc', $lang->get('telephone_cc')); ?>
    <?php echo $forms->input('Registrant.phone', $lang->get('telephone')); ?>

    <?php echo $forms->input('Registrant.fax_cc', $lang->get('fax_cc')); ?>
    <?php echo $forms->input('Registrant.fax', $lang->get('fax')); ?>

    <?php echo $forms->checkbox('cloneRegistrantContacts', $lang->get('use_registrant_contact')); ?>
    <div class="clearfix"></div>
</div>