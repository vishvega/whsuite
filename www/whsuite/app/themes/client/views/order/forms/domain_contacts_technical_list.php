<?php if(!empty($technical_contacts)): ?>
    <?php echo $forms->select('technical_contact', $lang->get('technical_contact'), array('options' => $technical_contacts)); ?>
<?php endif; ?>
<div id="new_technical_contact">
    <?php echo $view->fetch('order/forms/domain_contacts_technical.php'); ?>
</div>