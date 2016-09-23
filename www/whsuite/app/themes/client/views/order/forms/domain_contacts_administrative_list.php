<?php if(!empty($administrative_contacts)): ?>
    <?php echo $forms->select('administrative_contact', $lang->get('administrative_contact'), array('options' => $administrative_contacts)); ?>
<?php endif; ?>
<div id="new_administrative_contact">
    <?php echo $view->fetch('order/forms/domain_contacts_administrative.php'); ?>
</div>