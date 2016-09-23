<?php echo $forms->input('Product.name', $lang->get('name')); ?>

<?php echo $forms->wysiwyg('Product.description', $lang->get('description')); ?>

<?php echo $forms->checkbox('Product.is_active', $lang->get('active')); ?>

<?php echo $forms->checkbox('Product.is_visible', $lang->get('is_visible')); ?>

<?php echo $forms->select('Product.email_template_id', $lang->get('email_template'), array('options' => $email_templates)); ?>
<span class="help-block"><?php echo $lang->get('product_email_template_help_text'); ?></span>

<?php echo $forms->input('Product.stock', $lang->get('stock_level')); ?>
<span class="help-block"><?php echo $lang->get('stock_level_help_text'); ?></span>

<?php echo $forms->input('Product.sort', $lang->get('sort'), array('placeholder' => '0')); ?>
