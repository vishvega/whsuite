<h4><?php echo $lang->get('add_addon'); ?></h4>
<?php echo $forms->open(array('action' => $router->generate('admin-service-add-addon', array('id' => $client->id, 'service_id' => $purchase->id)))); ?>
    <?php echo $forms->select('addon_id', $lang->get('addon'), array('options' => $addons_list)); ?>
    <p class="text-center"><?php echo $forms->submit('submit', $lang->get('add_addon')); ?></p>
<?php echo $forms->close(); ?>

<h4><?php echo $lang->get('existing_addons'); ?></h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo $lang->get('addon'); ?></th>
            <th width="10%" class="text-center"><?php echo $lang->get('status'); ?></th>
            <th width="10%" class="text-center"><?php echo $lang->get('manage'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(count($purchased_addons) > 0):
            foreach($purchased_addons as $addon):
                $addon_details = $addon->ProductAddon()->first();
        ?>
        <tr>
            <td><?php echo $addon_details->name; ?></td>
            <td class="text-center">
                <?php if($addon->is_active): ?>
                    <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                <?php else: ?>
                    <span class="label label-danger"><?php echo $lang->get('inactive'); ?></span>
                <?php endif; ?>
            </td>
            <td class="text-center"><a href="<?php echo $router->generate('admin-service-manage-addon', array('id' => $client->id, 'service_id' => $purchase->id, 'addon_purchase_id' => $addon->id)); ?>"><?php echo $lang->get('manage'); ?></a></td>
        </tr>
        <?php
            endforeach;
        else:
        ?>
        <tr>
            <td colspan="3" class="text-center"><?php echo $lang->get('no_product_addons_purchased'); ?></td>
        </tr>
        <?php
        endif;
        ?>
    </tbody>
</table>
