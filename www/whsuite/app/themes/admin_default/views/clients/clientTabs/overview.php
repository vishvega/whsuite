<div class="row">
    <div class="col-md-6 col-sm-12">
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="row">
            <div class="col-md-6 col-sm-12 text-center">
                <div class="well well-stats">
                    <div>
                        <?php
                        if ($client->status == '1'):
                            echo $lang->get('active');
                        elseif($client->status == '2'):
                            echo $lang->get('suspended');
                        elseif($client->status == '3'):
                            echo $lang->get('closed');
                        elseif($client->status == '0'):
                            echo $lang->get('pending');
                        else:
                            echo $lang->get('not_available');
                        endif;
                        ?>
                    </div>
                    <small><?php echo $lang->get('account_status'); ?></small>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 text-center">
                <div class="well well-stats">
                    <div><?php echo $active_products; ?></div>
                    <small><?php echo $lang->get('active_products'); ?></small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-secondary">
                    <div class="panel-heading"><?php echo $lang->get('client_statistics'); ?></div>
                    <div class="panel-content panel-table">
                        <table class="table table-striped table-condensed">
                            <tbody>
                                <tr>
                                    <td><strong><?php echo $lang->get('date_created'); ?>:</strong></td>
                                    <td>
                                        <?php
                                            $Carbon = \Carbon\Carbon::parse(
                                                $client->created_at,
                                                $date['timezone']
                                            );
                                            echo $Carbon->format($date['short_date']);
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo $lang->get('last_login'); ?>:</strong></td>
                                    <td>
                                        <?php
                                        if ($client->last_login):
                                            $Carbon = \Carbon\Carbon::parse(
                                                $client->last_login,
                                                $date['timezone']
                                            );
                                            echo $Carbon->format($date['short_datetime']);
                                        else:
                                            echo $lang->get('never');
                                        endif;
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo $lang->get('last_ip_address'); ?>:</strong></td>
                                    <td>
                                        <?php if($client->last_ip !=''): ?>
                                            <?php echo $client->last_ip; ?> (<?php echo $client->last_hostname; ?>)
                                        <?php else: ?>
                                            <?php echo $lang->get('not_available'); ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td><strong><?php echo $lang->get('currency'); ?>:</td>
                                    <td><?php echo $currency->code; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo $lang->get('total_products'); ?>:</td>
                                    <td><?php echo $total_products; ?></td>
                                </tr>
                                <tr>
                                    <td><strong><?php echo $lang->get('active_products'); ?>:</td>
                                    <td><?php echo $active_products; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-secondary">
            <div class="panel-heading"><?php echo $lang->get('account_balance'); ?></div>
            <div class="panel-content">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo $lang->get('currency'); ?></th>
                            <th><?php echo $lang->get('total'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if(count($client_credit) > 0):
                            foreach ($client_credit as $currency_code => $balance):
                        ?>
                        <tr>
                            <td><?php echo $currency_code; ?></td>
                            <td><?php echo App::get('money')->format($balance, $currency_code); ?></td>
                        </tr>
                        <?php
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="2" class="text-center">
                                <?php echo $lang->get('not_available'); ?>
                            </td>
                        </tr>
                        <?php
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
