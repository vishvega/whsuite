<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-menulink-edit', array('id' => $menu->id, 'link_id' => $link->id)))); ?>

                                <?php echo $forms->input('MenuLink.title', $lang->get('title')); ?>
                                <?php echo $forms->input('MenuLink.url', $lang->get('url')); ?>
                                <?php echo $forms->select('MenuLink.target', $lang->get('target'), array('options' => $targets)); ?>
                                <?php echo $forms->select('MenuLink.is_link', $lang->get('link_type'), array('options' => $link_types)); ?>
                                <?php echo $forms->select('MenuLink.parent_id', $lang->get('parent'), array('options' => $parent_links)); ?>
                                <?php echo $forms->input('MenuLink.sort', $lang->get('sort')); ?>
                                <?php echo $forms->checkbox('MenuLink.clients_only', $lang->get('clients_only')); ?>
                                <?php echo $forms->input('MenuLink.class', $lang->get('class')); ?>

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
