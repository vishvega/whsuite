<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php
                                echo $forms->open(array(
                                    'role' => 'form',
                                    'action' => $router->generate('admin-staff-myprofile'),
                                    'id' => 'profile-save'
                                ));

                                foreach ($fields as $field => $attr):

                                    // set the vars up into a common format
                                    if (! is_array($attr)):

                                        $field = $attr;
                                        $attr = array();
                                    endif;

                                    // skip the staff group placeholder

                                    if ($field == 'Staff.Dashboard'):
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
                                    <legend><?php echo $lang->get('shortcuts'); ?></legend>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <h4><?php echo $lang->get('available_shortcuts'); ?></h4>

                                            <ul id="available" class="connectedSortable-shortcuts">
                                                <?php if (isset($available_shortcuts) && ! empty($available_shortcuts)): ?>

                                                    <?php foreach ($available_shortcuts as $id => $shortcut): ?>

                                                        <li data-shortcut-id="<?php echo $id; ?>">
                                                            <?php echo $lang->get($shortcut); ?>
                                                        </li>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <div class="col-md-6">
                                            <h4><?php echo $lang->get('selected_shortcuts'); ?></h4>

                                            <ul id="selected" class="connectedSortable-shortcuts">
                                                <?php if (isset($selected_shortcuts) && ! empty($selected_shortcuts)): ?>

                                                    <?php foreach ($selected_shortcuts as $id => $shortcut): ?>

                                                        <li data-shortcut-id="<?php echo $shortcut->id; ?>">
                                                            <?php echo $lang->get($shortcut->name); ?>
                                                        </li>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <?php echo $forms->hidden('data.Staff.Shortcut'); ?>
                                </fieldset>

                                <fieldset>
                                    <legend><?php echo $lang->get('widgets'); ?></legend>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <h4><?php echo $lang->get('available_widgets'); ?></h4>

                                            <ul id="available" class="connectedSortable-widgets">
                                                <?php if (isset($available_widgets) && ! empty($available_widgets)): ?>

                                                    <?php foreach ($available_widgets as $id => $widget): ?>

                                                        <li data-widget-id="<?php echo $id; ?>">
                                                            <?php echo $lang->get($widget); ?>
                                                        </li>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <div class="col-md-6">
                                            <h4><?php echo $lang->get('selected_widgets'); ?></h4>

                                            <ul id="selected" class="connectedSortable-widgets">
                                                <?php if (isset($selected_widgets) && ! empty($selected_widgets)): ?>

                                                    <?php foreach ($selected_widgets as $id => $widget): ?>

                                                        <li data-widget-id="<?php echo $widget->id; ?>">
                                                            <?php echo $lang->get($widget->name); ?>
                                                        </li>

                                                    <?php endforeach; ?>

                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <?php echo $forms->hidden('data.Staff.Widget'); ?>

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
