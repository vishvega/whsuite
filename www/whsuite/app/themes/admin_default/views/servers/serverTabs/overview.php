<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('server_details'); ?></div>
            <div class="panel-content panel-table">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong><?php echo $lang->get('name'); ?>:</strong></td>
                            <td><?php echo $server->name; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('hostname'); ?>:</strong></td>
                            <td><?php echo $server->hostname; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('main_ip'); ?>:</strong></td>
                            <td><?php echo $server->main_ip; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('location'); ?>:</strong></td>
                            <td><?php echo $server->location; ?></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('username'); ?>:</strong></td>
                            <td>
                                <?php
                                if(!empty($server->username)):
                                    echo App::get('security')->decrypt($server->username);
                                endif;
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('password'); ?>:</strong></td>
                            <td><i><?php echo $lang->get('hidden'); ?></i></td>
                        </tr>
                        <tr>
                            <td><strong><?php echo $lang->get('accounts'); ?>:</strong></td>
                            <td><?php echo $server->totalAccounts(); ?>/<?php echo $server->max_accounts; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php if(isset($addon_details)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('server_module_details'); ?></div>
            <div class="panel-content panel-table">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td><strong><?php echo $lang->get('server_module'); ?>:</strong></td>
                            <td><?php echo $addon_details['name']; ?></td>
                        </tr>
                        <?php if(!empty($custom_field_data['fields'])) : ?>
                            <?php foreach($custom_field_data['fields'] as $field): ?>
                            <tr>
                                <td><strong><?php echo $lang->get($field['title']); ?></strong></td>
                                <td>
                                    <?php
                                    if(!empty($field['value']['value'])):
                                        echo App::get('security')->decrypt($field['value']['value']);
                                    endif;
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
