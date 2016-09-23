<?php echo $view->fetch('elements/header.php'); ?>

    <div class="content-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-secondary">
                        <div class="panel-heading"><?php echo $title; ?></div>
                        <div class="panel-content panel-table">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo $lang->get('user'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('action_type'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('description'); ?></th>
                                        <th data-hide="phone"><?php echo $lang->get('date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($logs) < 1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            if(empty($log->client_id)):
                                                $user = $log->staff()->first();
                                                $user_type = $lang->get('staff');
                                            else:
                                                $user = $log->client()->first();
                                                $user_type = $lang->get('client');
                                            endif;

                                            echo $user->first_name.' '.$user->last_name.' ('.$user_type.')';
                                            ?>
                                            (<?php echo $lang->get('ip'); ?>: <?php echo $log->ip_address; ?>)
                                        </td>
                                        <td><?php echo $log->action_type; ?></td>
                                        <td><?php echo $log->action; ?></td>
                                        <td>
                                            <?php
                                                $Carbon = \Carbon\Carbon::parse(
                                                    $log->created_at,
                                                    $date['timezone']
                                                );
                                                echo $Carbon->format($date['short_datetime']);
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><?php echo $pagination; ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo $view->fetch('elements/footer.php');
