<?php echo $forms->hidden('domain', array('value' => $domain)); ?>
<?php echo $forms->hidden('billing_period', array('value' => $pricing->id)); ?>
<?php echo $forms->hidden('action', array('value' => $action)); ?>
<?php echo $forms->hidden('extension_id'); ?>
<div class="well">
    <h3 class="nomargin"><?php echo $domain; ?></h3>
    <?php echo $pricing->years; ?>
    <?php if($pricing->years == 1): ?>
        <?php echo $lang->get('year'); ?>
    <?php else: ?>
        <?php echo $lang->get('years'); ?>
    <?php endif; ?>

    <?php if($action == 'register'): ?>
    - <?php echo App::get('money')->format($pricing->registration, $pricing->currency_id, false, true); ?>
    <?php else: ?>
    - <?php echo App::get('money')->format($pricing->transfer, $pricing->currency_id, false, true); ?>
    <?php endif ?>
    (<?php echo $lang->get($action); ?>)
</div>
<?php echo $forms->input('nameservers[]', $lang->get('nameserver')); ?>
<?php echo $forms->input('nameservers[]', $lang->get('nameserver')); ?>
<?php echo $forms->input('nameservers[]', $lang->get('nameserver')); ?>
<?php echo $forms->input('nameservers[]', $lang->get('nameserver')); ?>
<?php if($action == 'transfer'): ?>
    <?php echo $forms->input('transfer_code', $lang->get('auth_code')); ?>
<?php endif; ?>

<fieldset>
    <legend><?php echo $lang->get('domain_contacts'); ?></legend>

    <?php if(count($registrant_contacts) > 1): ?>
        <?php echo $view->fetch('order/forms/domain_contacts_registrant_list.php'); ?>
    <?php else: ?>
        <?php echo $view->fetch('order/forms/domain_contacts_registrant.php'); ?>
    <?php endif; ?>

    <?php if(count($administrative_contacts) > 1): ?>
        <?php echo $view->fetch('order/forms/domain_contacts_administrative_list.php'); ?>
    <?php else: ?>
        <?php echo $view->fetch('order/forms/domain_contacts_administrative.php'); ?>
    <?php endif; ?>

    <?php if(count($technical_contacts) > 1): ?>
        <?php echo $view->fetch('order/forms/domain_contacts_technical_list.php'); ?>
    <?php else: ?>
        <?php echo $view->fetch('order/forms/domain_contacts_technical.php'); ?>
    <?php endif; ?>

    <?php if(count($billing_contacts) > 1): ?>
        <?php echo $view->fetch('order/forms/domain_contacts_billing_list.php'); ?>
    <?php else: ?>
        <?php echo $view->fetch('order/forms/domain_contacts_billing.php'); ?>
    <?php endif; ?>
</fieldset>
<?php echo $registration_fields; ?>


<?php echo $assets->script('order-contacts.js'); ?>

