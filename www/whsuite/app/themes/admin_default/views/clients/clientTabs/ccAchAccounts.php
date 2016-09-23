<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('cc_ach_accounts'); ?></div>
    <div class="panel-content">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('name'); ?></th>
                    <th><?php echo $lang->get('type'); ?></th>
                    <th data-hide="phone"><?php echo $lang->get('last4'); ?></th>
                    <th class="text-center"><?php echo $lang->get('status'); ?></th>
                    <th class="text-right"><?php echo $lang->get('manage'); ?></th>
                </tr>
            </thead>

            <tbody>
            <?php if (count($payment_accounts) > 0): ?>
                <?php foreach ($payment_accounts as $account): ?>
                    <tr>
                        <td><?php echo $account['data']->first_name.' '.$account['data']->last_name; ?></td>
                        <td>
                            <?php
                            if($account['type'] == 'cc'):
                                echo $lang->get('credit_card');
                            elseif($account['type'] == 'ach'):
                                echo $lang->get('automated_clearing_house');
                            endif;
                                echo ' ('.$lang->get($account['data']->account_type).')';
                            ?>
                        </td>
                        <td>
                            <?php
                                echo $account['data']->account_last4
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if($account['data']->is_active == '1'): ?>
                                <span class="label label-success"><?php echo $lang->get('active'); ?></span>
                            <?php else: ?>
                                <span class="label label-danger"><?php echo $lang->get('inactive'); ?></span>
                            <?php endif; ?>

                            <?php if($account['type'] == 'cc' && $account['data']->is_default == '1'): ?>
                                <span class="label label-info"><?php echo $lang->get('default_cc'); ?></span>
                            <?php elseif($account['type'] == 'ach' && $account['data']->is_default == '1'): ?>
                                <span class="label label-info"><?php echo $lang->get('default_ach'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php if($account['type'] == 'cc'): ?>
                                <a href="<?php echo $router->generate('admin-clientcc-edit', array('id' => $client->id, 'cc_id' => $account['data']->id)); ?>" class="btn btn-small btn-primary"><?php echo $lang->get('manage'); ?></a>
                            <?php elseif($account['type'] == 'ach'): ?>
                                <a href="<?php echo $router->generate('admin-clientach-edit', array('id' => $client->id, 'ach_id' => $account['data']->id)); ?>" class="btn btn-small btn-primary"><?php echo $lang->get('manage'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="text-center"><?php echo $lang->get('client_no_payment_accounts'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="row">
                            <div class="col-lg-6 text-center">
                                <?php if (\App::get('configs')->get('settings.billing.store_credit_cards') == 1): ?>
                                    <a href="<?php echo $router->generate('admin-clientcc-add', array('id' => $client->id)); ?>" class="btn btn-small btn-primary">
                                        <i class="fa fa-plus"></i> <?php echo $lang->get('new_cc'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-6 text-center">
                                <?php if (\App::get('configs')->get('settings.billing.store_ach') == 1): ?>
                                    <a href="<?php echo $router->generate('admin-clientach-add', array('id' => $client->id)); ?>" class="btn btn-small btn-primary">
                                        <i class="fa fa-plus"></i> <?php echo $lang->get('new_ach'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
