<div class="panel panel-secondary">
    <div class="panel-heading"><?php echo $lang->get('client_contact_details'); ?></div>
    <div class="panel-content panel-table">
        <table class="table table-striped table-condensed">
            <tbody>
                <tr>
                    <td><strong><?php echo $lang->get('name'); ?>:</strong></td>
                    <td><?php echo $client->first_name; ?> <?php echo $client->last_name; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('company'); ?>:</strong></td>
                    <td><?php echo $client->company; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('email'); ?>:</strong></td>
                    <td><?php echo $client->email; ?></td>
                </tr>
                <tr>
                    <td>
                        <strong><?php echo $lang->get('address'); ?>:</strong>
                    </td>
                    <td>
                        <?php echo $client->address1; ?><br>
                        <?php echo $client->address2; ?><br>
                        <?php echo $client->city; ?><br>
                        <?php echo $client->state; ?><br>
                        <?php echo $client->postcode; ?><br>
                        <?php echo $client->country; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('telephone'); ?></strong></td>
                    <td><?php echo $client->phone; ?></td>
                </tr>
                <?php if(isset($client_credit)): ?>
                <tr>
                    <td><strong><?php echo $lang->get('account_balance'); ?>:</strong></td>
                    <td>
                        <?php
                        if(count($client_credit) > 0):
                            foreach ($client_credit as $currency_code => $balance):
                        ?>
                                <?php echo App::get('money')->format($balance, $currency_code); ?> (<?php echo $currency_code; ?>)<br>
                        <?php
                            endforeach;
                        else:
                            echo $lang->get('not_available');
                        endif;
                        ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3" class="text-center">
                        <?php if($client->activated == '0'): ?>
                            <a href="<?php echo $router->generate('admin-client-activate', array('id' => $client->id)); ?>" class="btn btn-primary btn-mini"><?php echo $lang->get('activate_client'); ?></a>
                        <?php else: ?>
                            <a href="<?php echo $router->generate('admin-client-login', array('id' => $client->id)); ?>" target="_blank" class="btn btn-primary btn-mini"><?php echo $lang->get('login_as_client'); ?></a>
                            <a href="<?php echo $router->generate('admin-client-email-new-password', array('id' => $client->id)); ?>" class="btn btn-primary btn-mini"><?php echo $lang->get('email_new_password'); ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
