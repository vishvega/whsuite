<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('name'); ?></th>
                            <th><?php echo $lang->get('type'); ?></th>
                            <th><?php echo $lang->get('manage'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($contacts) > 0): ?>
                            <?php foreach($contacts as $contact): ?>
                                <tr>
                                    <td><?php echo $contact->first_name; ?> <?php echo $contact->last_name; ?> (<?php echo $contact->email; ?>)</td>
                                    <td><?php echo $lang->get($contact->contact_type); ?></td>
                                    <td>
                                        <a href="<?php echo $router->generate('client-manage-contact', array('contact_id' => $contact->id)); ?>" class="btn btn-primary btn-sm">
                                            <?php echo $lang->get('manage'); ?>
                                        </a>

                                        <a href="<?php echo $router->generate('client-delete-contact', array('contact_id' => $contact->id)); ?>" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>');">
                                            <?php echo $lang->get('delete'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">
                                    <?php echo $lang->get('no_results_found'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="3" class="text-center">
                                <a href="<?php echo $router->generate('client-create-contact'); ?>" class="btn btn-primary">
                                    <?php echo $lang->get('new_contact'); ?>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">
                                <?php echo $pagination; ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
