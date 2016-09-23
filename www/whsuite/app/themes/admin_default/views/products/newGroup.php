<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-productgroup-add'))); ?>

                                <?php echo $forms->input('Group.name', $lang->get('name')); ?>

                                <?php echo $forms->textarea('Group.description', $lang->get('description')); ?>

                                <?php echo $forms->checkbox('Group.is_visible', $lang->get('is_visible')); ?>

                                <?php echo $forms->input('Group.sort', $lang->get('sort')); ?>

                                <?php echo $group->customFields(false); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('add_product_group')); ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
