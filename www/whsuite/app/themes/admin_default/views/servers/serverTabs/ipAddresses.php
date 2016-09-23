<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('add_ip_addresses'); ?></div>
            <div class="panel-content">
                <?php echo $forms->open(array('action' => $router->generate('admin-serverip-add', array('id' => $group->id, 'server_id' => $server->id)))); ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-inline">
                                <label for="startipA"><?php echo $lang->get('starting_ip'); ?></label>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('startip.a', false, array('class' => '')); ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('startip.b', false, array('class' => '')); ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('startip.c', false, array('class' => '')); ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('startip.d', false, array('class' => '')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-inline">
                                <label for="endipA"><?php echo $lang->get('ending_ip'); ?></label>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('endip.a', false, array('class' => 'disabled', 'disabled' => 'disabled')); ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('endip.b', false, array('class' => 'disabled', 'disabled' => 'disabled')); ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('endip.c', false, array('class' => 'disabled', 'disabled' => 'disabled')); ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?php echo $forms->input('endip.d', false, array('class' => '')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php echo $forms->submit('submit', $lang->get('add_ip_addresses')); ?>
                    </div>

                <?php echo $forms->close(); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <?php echo $forms->open(array('action' => $router->generate('admin-serverip-delete', array('id' => $group->id, 'server_id' => $server->id)), 'class' => 'form-inline')); ?>
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('ip_addresses'); ?></div>
            <div class="panel-content panel-table">

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('ip_address'); ?></th>
                            <th><?php echo $lang->get('account'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $server->main_ip; ?></td>
                            <td><?php echo $lang->get('main_ip'); ?></td>
                            <td></td>
                        </tr>
                    <?php if($ip_addresses->count() > 0): ?>

                        <?php foreach($ip_addresses as $ip): ?>
                            <tr>
                                <td><?php echo $ip->ip_address; ?></td>
                                <td>
                                    <?php if($ip->product_purchase_id > 0): ?>
                                        <?php
                                        $purchase = $ip->ProductPurchase()->first();
                                        $hosting = $purchase->Hosting()->first();
                                        ?>
                                        <?php echo $hosting->domain; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php
                                    if($ip->product_purchase_id == '0'):
                                        echo $forms->checkbox('ip.'.$ip->id, null);
                                    endif;
                                    ?>
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


<script>
$('#startipA').change(function() {
    $('#endipA').val($(this).val());
});

$('#startipB').change(function() {
    $('#endipB').val($(this).val());
});

$('#startipC').change(function() {
    $('#endipC').val($(this).val());
});

</script>
