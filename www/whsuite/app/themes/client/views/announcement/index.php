<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo $title; ?></div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('subject'); ?></th>
                            <th class="text-center"><?php echo $lang->get('date'); ?></th>
                            <th class="text-right"><?php echo $lang->get('view'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($announcements) > 0): ?>
                            <?php foreach($announcements as $announcement): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo $router->generate('client-announcement', array('id' => $announcement->id)); ?>">
                                            <?php echo $announcement->title; ?>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $publishDate = \Carbon\Carbon::parse(
                                                $announcement->publish_date,
                                                $date['timezone']
                                            );
                                            echo $publishDate->format($date['short_datetime']);
                                        ?>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?php echo $router->generate('client-announcement', array('id' => $announcement->id)); ?>" class="btn btn-primary btn-xs">
                                            <?php echo $lang->get('view'); ?>
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

                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
