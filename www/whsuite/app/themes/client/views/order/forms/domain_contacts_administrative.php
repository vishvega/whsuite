<div class="well" id="containerAdministrativeContacts">
    <h4><?php echo $lang->get('administrative_contact_details'); ?></h4>
    <?php echo $forms->select('Administrative.title', $lang->get('title'), array('options' => $contact_titles)); ?>

    <?php echo $forms->input('Administrative.first_name', $lang->get('first_name')); ?>
    <?php echo $forms->input('Administrative.last_name', $lang->get('last_name')); ?>
    <?php echo $forms->input('Administrative.company', $lang->get('company')); ?>
    <?php echo $forms->input('Administrative.job_title', $lang->get('job_title')); ?>

    <?php echo $forms->input('Administrative.email', $lang->get('email')); ?>
    <?php echo $forms->input('Administrative.address1', $lang->get('address1')); ?>
    <?php echo $forms->input('Administrative.address2', $lang->get('address2')); ?>
    <?php echo $forms->input('Administrative.address3', $lang->get('address3')); ?>
    <?php echo $forms->input('Administrative.city', $lang->get('city')); ?>
    <?php echo $forms->input('Administrative.state', $lang->get('state')); ?>
    <?php echo $forms->input('Administrative.postcode', $lang->get('postcode')); ?>
    <?php echo $forms->select('Administrative.country', $lang->get('country'), array('options' => $country_list)); ?>

    <?php echo $forms->input('Administrative.phone_cc', $lang->get('telephone_cc')); ?>
    <?php echo $forms->input('Administrative.phone', $lang->get('telephone')); ?>

    <?php echo $forms->input('Administrative.fax_cc', $lang->get('fax_cc')); ?>
    <?php echo $forms->input('Administrative.fax', $lang->get('fax')); ?>
</div>