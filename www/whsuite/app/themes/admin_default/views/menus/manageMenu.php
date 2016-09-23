<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $lang->get($menu->name); ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-menu-manage', array('id' => $menu->id)))); ?>

                                <?php echo $forms->input('MenuGroup.name', $lang->get('name')); ?>

                                <div class="form-actions">
                                    <?php echo $forms->submit('submit', $lang->get('save')); ?>
                                    <?php if($menu->id > 2): ?>
                                        <a href="#" class="btn btn-danger pull-right" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');"><?php echo $lang->get('delete_menu'); ?></a>
                                    <?php endif; ?>
                                </div>
                            <?php echo $forms->close(); ?>
                        </div>
                    </div>

                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $lang->get('add_link'); ?></div>
                        <div class="panel-content">
                            <?php echo $forms->open(array('action' => $router->generate('admin-menulink-add', array('id' => $menu->id)))); ?>

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

                <div class="col-md-7">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><?php echo $lang->get('manage_links'); ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('title'); ?></th>
                                        <th><?php echo $lang->get('url'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($links->count() > 0): ?>
                                        <?php foreach($links as $link): ?>
                                            <tr>
                                                <td><?php echo $lang->get($link->title); ?></td>
                                                <td>
                                                    <?php if($link->is_link == '0'): ?>
                                                        <?php echo $router->generate($link->url); ?>
                                                    <?php else: ?>
                                                        <?php echo $link->url; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <a href="<?php echo $router->generate('admin-menulink-edit', array('id' => $menu->id, 'link_id' => $link->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('edit'); ?></a>
                                                    <a href="<?php echo $router->generate('admin-menulink-delete', array('id' => $menu->id, 'link_id' => $link->id)); ?>" class="btn btn-danger btn-small" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');"><?php echo $lang->get('delete'); ?></a>
                                                </td>
                                            </tr>
                                            <?php if($link->children()->count() > 0): ?>
                                                <?php foreach($link->children()->get() as $child): ?>
                                                    <tr>
                                                        <td> -- <?php echo $lang->get($child->title); ?></a></td>
                                                        <td>
                                                            <?php if($child->is_link == '0'): ?>
                                                                <?php echo $router->generate($child->url); ?>
                                                            <?php else: ?>
                                                                <?php echo $child->url; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-right">
                                                            <a href="<?php echo $router->generate('admin-menulink-edit', array('id' => $menu->id, 'link_id' => $child->id)); ?>" class="btn btn-primary btn-small"><?php echo $lang->get('edit'); ?></a>
                                                            <a href="<?php echo $router->generate('admin-menulink-delete', array('id' => $menu->id, 'link_id' => $child->id)); ?>" class="btn btn-danger btn-small" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');"><?php echo $lang->get('delete'); ?></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">
                                                <?php echo $lang->get('no_results_found'); ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $view->fetch('elements/footer.php'); ?>
