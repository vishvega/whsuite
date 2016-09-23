<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('email'); ?></div>
    <div class="panel-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('subject'); ?></th>
                    <th><?php echo $lang->get('date_sent'); ?></th>
                    <th class="text-right"><?php echo $lang->get('view'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if($emails->count() > 0): ?>
                <?php foreach ($emails as $email): ?>
                <tr>
                    <td><?php echo App::get('str')->limit($email->subject, 80); ?></td>
                    <td>
                        <?php
                            $Carbon = \Carbon\Carbon::parse(
                                $email->created_at,
                                $date['timezone']
                            );
                            echo $Carbon->format($date['short_date']);
                        ?>
                    </td>
                    <td class="text-right">
                        <a href="<?php echo $router->generate('admin-clientemail-view', array('id' => $client->id, 'email_id' => $email->id)); ?>" class="emailModal btn btn-primary btn-small">
                            <?php echo $lang->get('view'); ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <div class="row">
                            <div class="col-sm-6 text-center">
                                <a href="<?php echo $router->generate('admin-clientemail-add', array('id' => $client->id)); ?>" class="btn btn-primary btn-small"><i class="fa fa-envelope-o"></i> <?php echo $lang->get('send_email'); ?></a>
                            </div>
                            <div class="col-sm-6 text-center">
                                <a href="<?php echo $router->generate('admin-clientemail', array('id' => $client->id)); ?>" class="btn btn-primary btn-small"><i class="fa fa-list"></i> <?php echo $lang->get('all_emails'); ?></a>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>