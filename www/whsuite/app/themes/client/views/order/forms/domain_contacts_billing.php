<div class="well" id="containerBillingContacts">
    <h4><?php echo $lang->get('billing_contact_details'); ?></h4>
    <?php echo $forms->select('Billing.title', $lang->get('title'), array('options' => $contact_titles)); ?>

    <?php echo $forms->input('Billing.first_name', $lang->get('first_name')); ?>
    <?php echo $forms->input('Billing.last_name', $lang->get('last_name')); ?>
    <?php echo $forms->input('Billing.company', $lang->get('company')); ?>
    <?php echo $forms->input('Billing.job_title', $lang->get('job_title')); ?>

    <?php echo $forms->input('Billing.email', $lang->get('email')); ?>
    <?php echo $forms->input('Billing.address1', $lang->get('address1')); ?>
    <?php echo $forms->input('Billing.address2', $lang->get('address2')); ?>
    <?php echo $forms->input('Billing.address3', $lang->get('address3')); ?>
    <?php echo $forms->input('Billing.city', $lang->get('city')); ?>
    <?php echo $forms->input('Billing.state', $lang->get('state')); ?>
    <?php echo $forms->input('Billing.postcode', $lang->get('postcode')); ?>
    <?php echo $forms->select('Billing.country', $lang->get('country'), array('options' => $country_list)); ?>

    <?php echo $forms->input('Billing.phone_cc', $lang->get('telephone_cc')); ?>
    <?php echo $forms->input('Billing.phone', $lang->get('telephone')); ?>

    <?php echo $forms->input('Billing.fax_cc', $lang->get('fax_cc')); ?>
    <?php echo $forms->input('Billing.fax', $lang->get('fax')); ?>
</div>