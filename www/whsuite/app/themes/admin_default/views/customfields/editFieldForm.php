<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php
                                echo $forms->open(
                                    array(
                                        'action' => $router->generate(
                                            'admin-custom-fields-edit-field',
                                            array(
                                                'id' => $group->id,
                                                'field_id' => $field->id
                                            )
                                        )
                                    )
                                );
                            ?>
                                <?php echo $forms->input('Field.slug', $lang->get('field_slug')); ?>
                                <?php echo $forms->input('Field.title', $lang->get('field_title')); ?>
                                <span class="help-block"><?php echo $lang->get('field_title_help_text'); ?></span>
                                <?php echo $forms->select('Field.type', $lang->get('field_type'), array('options' => $field_types)); ?>
                                <?php echo $forms->input('Field.help_text', $lang->get('field_help_text')); ?>
                                <?php echo $forms->input('Field.placeholder', $lang->get('field_placeholder')); ?>
                                <?php echo $forms->input('Field.value_options', $lang->get('field_value_options')); ?>
                                <span class="help-block"><?php echo $lang->get('field_value_options_help_text'); ?></span>
                                <?php echo $forms->checkbox('Field.is_editable', $lang->get('field_is_editable')); ?>
                                <?php echo $forms->checkbox('Field.is_staff_only', $lang->get('field_is_staff_only')); ?>
                                <?php echo $forms->input('Field.validation_rules', $lang->get('field_validation_rules')); ?>
                                <span class="help-block"><?php echo $lang->get('field_validation_rules_help_text'); ?></span>
                                <?php echo $forms->input('Field.custom_regex', $lang->get('field_custom_regex')); ?>
                                <?php echo $forms->input('Field.sort', $lang->get('field_sort')); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
