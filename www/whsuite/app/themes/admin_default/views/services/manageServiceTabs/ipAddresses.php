<?php
    echo $forms->open(
        array(
            'action' => $router->generate('admin-service-edit-ips',
            array(
                'id' => $client->id,
                'service_id' => $purchase->id)
            ),
            'role' => 'form'
        )
    );
?>

    <fieldset>
        <legend><?php echo $lang->get('assign_an_ip_address'); ?></legend>
        <?php echo $forms->select('assign_ip', $lang->get('available_ip_addresses'), array('options' => $server_ips)); ?>
    </fieldset>
    <fieldset>
        <legend><?php echo $lang->get('assigned_ip_addresses'); ?></legend>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('ip_address'); ?></th>
                    <th width="10%"><?php echo $lang->get('unassign'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($assigned_ips as $id => $ip): ?>
                <tr>
                    <td><?php echo $ip; ?></td>
                    <td><?php echo $forms->checkbox('assigned_ip['.$id.']', false); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if(count($assigned_ips) == 0): ?>
                <tr>
                    <td colspan="2" class="text-center"><?php echo $lang->get('no_ip_addresses_assigned'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </fieldset>

    <div class="form-actions">
        <?php echo $forms->submit('submit', $lang->get('save')); ?>
    </div>
<?php echo $forms->close(); ?>
