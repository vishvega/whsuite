<?php echo $forms->input('Product.auto_suspend_days', $lang->get('auto_suspend_days'), (!isset($product->auto_suspend_days) ? array('value' => '14') : array())); ?>
<span class="help-block"><?php echo $lang->get('auto_suspend_days_help_text'); ?></span>

<?php echo $forms->select('Product.suspend_email_template_id', $lang->get('auto_suspend_email_template'), array('options' => $email_templates)); ?>

<?php echo $forms->input('Product.auto_terminate_days', $lang->get('auto_terminate_days'), (!isset($product->auto_terminate_days) ? array('value' => '30') : array())); ?>
<span class="help-block"><?php echo $lang->get('auto_terminate_days_help_text'); ?></span>

<?php echo $forms->select('Product.terminate_email_template_id', $lang->get('auto_terminate_email_template'), array('options' => $email_templates)); ?>

<?php echo $forms->checkbox('Product.is_taxed', $lang->get('apply_tax_where_applicable')); ?>
