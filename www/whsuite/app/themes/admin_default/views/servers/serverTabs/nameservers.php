<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('add_nameserver'); ?></div>
            <div class="panel-content">
                <?php echo $forms->open(array('action' => $router->generate('admin-servernameserver-add', array('id' => $group->id, 'server_id' => $server->id)), 'class' => 'form-vertical')); ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <?php echo $forms->input('Nameserver.hostname', $lang->get('hostname')); ?>
                        </div>
                        <div class="col-lg-6">
                            <?php echo $forms->input('Nameserver.ip_address', $lang->get('ip_address')); ?>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('add_nameserver')); ?>
                    </div>

                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <?php echo $forms->open(array('action' => $router->generate('admin-servernameserver-delete', array('id' => $group->id, 'server_id' => $server->id)), 'class' => 'form-inline')); ?>
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('nameservers'); ?></div>
            <div class="panel-content panel-table">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('hostname'); ?></th>
                            <th><?php echo $lang->get('ip_address'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if($nameservers->count() < 1): ?>
                        <tr>
                            <td colspan="3" class="text-center"><?php echo $lang->get('no_results_found'); ?></td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($nameservers as $ns): ?>
                            <tr>
                                <td><?php echo $ns->hostname; ?></td>
                                <td><?php echo $ns->ip_address; ?></td>
                                <td class="text-right">
                                    <?php echo $forms->checkbox('nameserver.'.$ns->id, null); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="text-right">
                                    <?php echo $forms->submit('submit', $lang->get('delete_selected'), array('class' => 'btn btn-danger btn-small')); ?>
                                </td>
                            </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <?php echo $forms->close(); ?>
</div>
