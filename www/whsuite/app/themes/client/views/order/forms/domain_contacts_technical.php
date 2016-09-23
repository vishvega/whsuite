<div class="well" id="containerTechnicalContacts">
    <h4><?php echo $lang->get('technical_contact_details'); ?></h4>
    <?php echo $forms->select('Technical.title', $lang->get('title'), array('options' => $contact_titles)); ?>

    <?php echo $forms->input('Technical.first_name', $lang->get('first_name')); ?>
    <?php echo $forms->input('Technical.last_name', $lang->get('last_name')); ?>
    <?php echo $forms->input('Technical.company', $lang->get('company')); ?>
    <?php echo $forms->input('Technical.job_title', $lang->get('job_title')); ?>

    <?php echo $forms->input('Technical.email', $lang->get('email')); ?>
    <?php echo $forms->input('Technical.address1', $lang->get('address1')); ?>
    <?php echo $forms->input('Technical.address2', $lang->get('address2')); ?>
    <?php echo $forms->input('Technical.address3', $lang->get('address3')); ?>
    <?php echo $forms->input('Technical.city', $lang->get('city')); ?>
    <?php echo $forms->input('Technical.state', $lang->get('state')); ?>
    <?php echo $forms->input('Technical.postcode', $lang->get('postcode')); ?>
    <?php echo $forms->select('Technical.country', $lang->get('country'), array('options' => $country_list)); ?>

    <?php echo $forms->input('Technical.phone_cc', $lang->get('telephone_cc')); ?>
    <?php echo $forms->input('Technical.phone', $lang->get('telephone')); ?>

    <?php echo $forms->input('Technical.fax_cc', $lang->get('fax_cc')); ?>
    <?php echo $forms->input('Technical.fax', $lang->get('fax')); ?>
</div>