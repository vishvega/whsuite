<?php echo $view->fetch('elements/header.php'); ?>
    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php
                                echo $forms->open(array(
                                    'role' => 'form',
                                    'action' => $page_url
                                ));

                                foreach ($fields as $field => $attr):

                                    // set the vars up into a common format
                                    if (! is_array($attr)):

                                        $field = $attr;
                                        $attr = array();
                                    endif;

                                    // skip the staff group placeholder

                                    if ($field == 'Staff.StaffGroup'):
                                        continue;
                                    endif;

                                    // check for a type, if none, we're gonna have to try and work it out
                                    if (! isset($attr['type'])):

                                        $attr['type'] = \App::get('formhelper')->getType($field);
                                    endif;

                                    // check if it's a select box, if so check for options.
                                    if ($attr['type'] == 'select' && (! isset($attr['options']) || empty($attr['options']))):

                                        $field_name = \App::get('formhelper')->getFieldName($field);

                                        // is a select box but we need options,
                                        // try and find based on field name
                                        if (isset($$field_name)):

                                            $attr['options'] = $$field_name;
                                        else:

                                            $attr['options'] = array();
                                        endif;
                                    endif;

                                    // check for label
                                    if (isset($attr['label'])):

                                        if (is_array($attr['label']) && ! empty($attr['label']['label'])):

                                            $label = $attr['label']['label'];
                                            unset($attr['label']['label']);

                                        elseif (! is_array($attr['label'])):
                                            $label = $attr['label'];
                                            unset($attr['label']);
                                        endif;
                                    endif;

                                    // could be a case where no label is still set
                                    // try find label form field name
                                    if (! isset($label)):

                                        $label = \App::get('formhelper')->getFieldName($field);
                                    endif;

                                    $label = $lang->get($label);

                                    // finally generate the input
                                    echo $forms->input('data.' . $field, $label, $attr);

                                    // unset vars to prevent errors
                                    unset($label);

                                endforeach;

                                echo $model_object->customFields(false);
                            ?>
                                <div class="clearfix"></div>
                                <fieldset>
                                    <legend><?php echo $lang->get('groups'); ?></legend>
                                    <div class="row">
                                    <?php foreach ($groups as $group): ?>

                                        <div class="col-3">

                                            <?php
                                                echo $forms->checkbox(
                                                    'data.Staff.StaffGroup.' . $group->id,
                                                    $group->name
                                                );
                                            ?>

                                        </div>
                                    <?php endforeach; ?>
                                    </div>
                                </fieldset>
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

<?php echo $view->fetch('elements/footer.php'); ?>
