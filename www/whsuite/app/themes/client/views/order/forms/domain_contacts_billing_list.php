<?php if(!empty($billing_contacts)): ?>
    <?php echo $forms->select('billing_contact', $lang->get('billing_contact'), array('options' => $billing_contacts)); ?>
<?php endif; ?>
<div id="new_billing_contact">
    <?php echo $view->fetch('order/forms/domain_contacts_billing.php'); ?>
</div>