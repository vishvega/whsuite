<?php if(!empty($registrant_contacts)): ?>
    <?php echo $forms->select('registrant_contact', $lang->get('registrant_contact'), array('options' => $registrant_contacts)); ?>
<?php endif; ?>
<div id="new_registrant_contact">
    <?php echo $view->fetch('order/forms/domain_contacts_registrant.php'); ?>
</div>