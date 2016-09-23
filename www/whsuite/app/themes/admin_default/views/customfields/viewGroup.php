<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('custom_field_group_details'); ?></div>
                        <div class="panel-content">
                            <?php if(DEV_MODE): ?>
                                <?php echo $forms->open(array('action' => $router->generate('admin-custom-fields-view-group', array('id' => $group->id)))); ?>

                                    <?php echo $forms->input('Group.slug', $lang->get('slug')); ?>
                                    <?php echo $forms->input('Group.name', $lang->get('name')); ?>
                                    <?php echo $forms->select('Group.addon_id', $lang->get('addon'), array('options' => $addons)); ?>
                                    <?php echo $forms->checkbox('Group.is_editable', $lang->get('fields_editable_by_staff')); ?>
                                    <?php echo $forms->checkbox('Group.is_active', $lang->get('active')); ?>

                                    <div class="form-actions">
                                        <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                    </div>
                                <?php echo $forms->close(); ?>
                            <?php else: ?>
                                <p><strong><?php echo $lang->get('slug'); ?>:</strong> <?php echo $group->slug; ?></p>
                                <p><strong><?php echo $lang->get('name'); ?>:</strong> <?php echo $lang->get($group->name); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $lang->get('custom_fields'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('title'); ?></th>
                                        <th><?php echo $lang->get('field_type'); ?></th>
                                        <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($fields) < 1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php foreach ($fields as $field): ?>
                                    <tr>
                                        <td><?php echo $lang->get($field->title); ?></td>
                                        <td><?php echo $lang->get($field->type); ?></td>
                                        <td class="text-right">
                                            <a href="<?php echo $router->generate('admin-custom-fields-edit-field', array('id' => $group->id, 'field_id' => $field->id)); ?>" class="btn btn-primary">
                                                <?php echo $lang->get('edit'); ?>
                                            </a>

                                            <a href="<?php echo $router->generate('admin-custom-fields-delete-field', array('id' => $group->id, 'field_id' => $field->id)); ?>" class="btn btn-danger" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                                                <?php echo $lang->get('delete'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
