<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('service'); ?></th>
                            <th class="text-center"><?php echo $lang->get('status'); ?></th>
                            <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($services) > 0): ?>
                            <?php foreach($services as $service): ?>
                                <tr>
                                    <td><?php echo $service->service_title; ?></td>
                                    <td class="text-center">
                                        <?php if($service->purchase->status == '1'): ?>
                                            <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                                        <?php elseif($service->purchase->status == '2'): ?>
                                            <span class="label label-warning"><?php echo $lang->get('suspended'); ?></span>
                                        <?php elseif($service->purchase->status == '3'): ?>
                                            <span class="label label-danger"><?php echo $lang->get('terminated'); ?></span>
                                        <?php else: ?>
                                            <span class="label label-default"><?php echo $lang->get('pending'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo $router->generate('client-manage-service', array('service_id' => $service->purchase->id)); ?>" class="btn btn-primary btn-xs">
                                            <?php echo $lang->get('manage'); ?>
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
